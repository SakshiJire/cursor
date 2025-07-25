import React, { useState, useEffect } from 'react';
import Layout from '../../components/Layout/Layout';
import LoadingSpinner from '../../components/Common/LoadingSpinner';
import Alert from '../../components/Common/Alert';
import feeService, { FeePayment, RecordPaymentRequest } from '../../services/feeService';

const FeePayments: React.FC = () => {
  const [payments, setPayments] = useState<FeePayment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [showPaymentForm, setShowPaymentForm] = useState(false);
  const [paymentFormData, setPaymentFormData] = useState<RecordPaymentRequest>({
    student_id: 0,
    fee_structure_id: 0,
    amount_paid: 0,
    payment_method: 'cash',
    transaction_id: '',
    remarks: '',
  });
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    fetchPayments();
  }, []);

  const fetchPayments = async () => {
    try {
      setLoading(true);
      const response = await feeService.getFeePayments();
      setPayments(response);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to fetch payments');
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setPaymentFormData(prev => ({
      ...prev,
      [name]: name === 'student_id' || name === 'fee_structure_id' || name === 'amount_paid' 
        ? Number(value) 
        : value
    }));
  };

  const handleSubmitPayment = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setError('');

    try {
      await feeService.recordPayment(paymentFormData);
      setSuccess('Payment recorded successfully!');
      setShowPaymentForm(false);
      setPaymentFormData({
        student_id: 0,
        fee_structure_id: 0,
        amount_paid: 0,
        payment_method: 'cash',
        transaction_id: '',
        remarks: '',
      });
      fetchPayments();
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to record payment');
    } finally {
      setSubmitting(false);
    }
  };

  const handleGenerateReceipt = async (paymentId: number) => {
    try {
      const blob = await feeService.generateReceipt(paymentId);
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `receipt-${paymentId}.pdf`;
      link.click();
      window.URL.revokeObjectURL(url);
    } catch (err: any) {
      setError('Failed to generate receipt');
    }
  };

  return (
    <Layout title="Fee Payments">
      <div className="container">
        {error && <Alert type="error" message={error} onClose={() => setError('')} />}
        {success && <Alert type="success" message={success} onClose={() => setSuccess('')} />}

        <div className="page-header">
          <div className="page-title">
            <h2>Fee Payments</h2>
            <p>Record and manage fee payments</p>
          </div>
          <div className="page-actions">
            <button 
              className="btn btn-primary"
              onClick={() => setShowPaymentForm(!showPaymentForm)}
            >
              {showPaymentForm ? 'Cancel' : '+ Record Payment'}
            </button>
          </div>
        </div>

        {showPaymentForm && (
          <div className="card">
            <div className="card-header">
              <h3>Record New Payment</h3>
            </div>
            <div className="card-body">
              <form onSubmit={handleSubmitPayment}>
                <div className="form-row">
                  <div className="form-group">
                    <label>Student ID</label>
                    <input
                      type="number"
                      name="student_id"
                      value={paymentFormData.student_id}
                      onChange={handleInputChange}
                      className="form-control"
                      required
                    />
                  </div>
                  <div className="form-group">
                    <label>Fee Structure ID</label>
                    <input
                      type="number"
                      name="fee_structure_id"
                      value={paymentFormData.fee_structure_id}
                      onChange={handleInputChange}
                      className="form-control"
                      required
                    />
                  </div>
                  <div className="form-group">
                    <label>Amount Paid</label>
                    <input
                      type="number"
                      name="amount_paid"
                      value={paymentFormData.amount_paid}
                      onChange={handleInputChange}
                      className="form-control"
                      min="0"
                      step="0.01"
                      required
                    />
                  </div>
                </div>
                <div className="form-row">
                  <div className="form-group">
                    <label>Payment Method</label>
                    <select
                      name="payment_method"
                      value={paymentFormData.payment_method}
                      onChange={handleInputChange}
                      className="form-control"
                      required
                    >
                      <option value="cash">Cash</option>
                      <option value="cheque">Cheque</option>
                      <option value="online">Online</option>
                      <option value="card">Card</option>
                      <option value="upi">UPI</option>
                    </select>
                  </div>
                  <div className="form-group">
                    <label>Transaction ID (Optional)</label>
                    <input
                      type="text"
                      name="transaction_id"
                      value={paymentFormData.transaction_id}
                      onChange={handleInputChange}
                      className="form-control"
                    />
                  </div>
                </div>
                <div className="form-group">
                  <label>Remarks (Optional)</label>
                  <textarea
                    name="remarks"
                    value={paymentFormData.remarks}
                    onChange={handleInputChange}
                    className="form-control"
                    rows={3}
                  />
                </div>
                <div className="form-actions">
                  <button 
                    type="submit" 
                    className="btn btn-primary"
                    disabled={submitting}
                  >
                    {submitting ? 'Recording...' : 'Record Payment'}
                  </button>
                  <button 
                    type="button" 
                    className="btn btn-secondary"
                    onClick={() => setShowPaymentForm(false)}
                  >
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}

        <div className="card">
          <div className="card-header">
            <h3>Payment Records</h3>
          </div>
          <div className="card-body">
            {loading ? (
              <LoadingSpinner message="Loading payments..." />
            ) : payments.length === 0 ? (
              <div className="no-data">
                <p>No payments found</p>
              </div>
            ) : (
              <div className="table-responsive">
                <table className="table">
                  <thead>
                    <tr>
                      <th>Receipt No.</th>
                      <th>Student</th>
                      <th>Amount</th>
                      <th>Payment Date</th>
                      <th>Method</th>
                      <th>Transaction ID</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {payments.map((payment) => (
                      <tr key={payment.id}>
                        <td>{payment.receipt_number}</td>
                        <td>
                          {payment.student && (
                            <div>
                              <strong>
                                {payment.student.user.first_name} {payment.student.user.last_name}
                              </strong>
                              <br />
                              <small>{payment.student.admission_number}</small>
                            </div>
                          )}
                        </td>
                        <td>₹{payment.total_amount.toFixed(2)}</td>
                        <td>{new Date(payment.payment_date).toLocaleDateString()}</td>
                        <td>{payment.payment_method}</td>
                        <td>{payment.transaction_id || '-'}</td>
                        <td>
                          <span className={`status ${payment.status}`}>
                            {payment.status}
                          </span>
                        </td>
                        <td>
                          <button
                            className="btn btn-sm btn-primary"
                            onClick={() => handleGenerateReceipt(payment.id)}
                          >
                            Receipt
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default FeePayments;