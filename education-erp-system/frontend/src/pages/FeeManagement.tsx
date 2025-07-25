import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

interface FeeStructure {
  id: number;
  fee_type: string;
  amount: number;
  frequency: string;
  due_date: string;
  class: {
    name: string;
    section: string;
  };
}

const FeeManagement: React.FC = () => {
  const navigate = useNavigate();
  const [feeStructures, setFeeStructures] = useState<FeeStructure[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchFeeStructures();
  }, []);

  const fetchFeeStructures = async () => {
    try {
      const token = localStorage.getItem('token');
      const response = await fetch('/api/fees/structures', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      const data = await response.json();
      if (data.success) {
        setFeeStructures(data.data);
      }
    } catch (error) {
      console.error('Error fetching fee structures:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container">
      <div className="header">
        <div className="container">
          <h1>Fee Management</h1>
        </div>
      </div>

      <div className="card">
        <div className="card-header flex justify-between align-center">
          <span>Fee Structures</span>
          <button className="btn btn-primary">
            Add New Fee Structure
          </button>
        </div>
        <div className="card-body">
          {loading ? (
            <div className="spinner"></div>
          ) : (
            <table className="table">
              <thead>
                <tr>
                  <th>Fee Type</th>
                  <th>Class</th>
                  <th>Amount</th>
                  <th>Frequency</th>
                  <th>Due Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {feeStructures.map((fee) => (
                  <tr key={fee.id}>
                    <td>{fee.fee_type}</td>
                    <td>{fee.class.name} {fee.class.section}</td>
                    <td>₹{fee.amount.toLocaleString()}</td>
                    <td>{fee.frequency}</td>
                    <td>{new Date(fee.due_date).toLocaleDateString()}</td>
                    <td>
                      <button className="btn btn-secondary">Edit</button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
      </div>

      <div className="card">
        <div className="card-header">
          Quick Actions
        </div>
        <div className="card-body">
          <div className="flex gap-4">
            <button className="btn btn-success">
              Record Payment
            </button>
            <button className="btn btn-secondary">
              Fee Collection Report
            </button>
            <button className="btn btn-secondary">
              Generate Receipts
            </button>
            <button
              onClick={() => navigate('/dashboard')}
              className="btn btn-secondary"
            >
              Back to Dashboard
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default FeeManagement;