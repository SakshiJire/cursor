import api from './api';

export interface FeeStructure {
  id: number;
  institute_id: number;
  class_id: number;
  fee_type: string;
  amount: number;
  frequency: string;
  due_date: string;
  late_fee_amount?: number;
  late_fee_days?: number;
  academic_year: string;
  status: string;
  class?: {
    id: number;
    name: string;
    section: string;
  };
}

export interface FeePayment {
  id: number;
  student_id: number;
  fee_structure_id: number;
  amount_paid: number;
  late_fee: number;
  total_amount: number;
  payment_date: string;
  payment_method: string;
  transaction_id?: string;
  receipt_number: string;
  remarks?: string;
  status: string;
  student?: {
    id: number;
    admission_number: string;
    user: {
      first_name: string;
      last_name: string;
    };
  };
  fee_structure?: FeeStructure;
}

export interface CreateFeeStructureRequest {
  class_id: number;
  fee_type: string;
  amount: number;
  frequency: string;
  due_date: string;
  late_fee_amount?: number;
  late_fee_days?: number;
  academic_year: string;
}

export interface RecordPaymentRequest {
  student_id: number;
  fee_structure_id: number;
  amount_paid: number;
  payment_method: string;
  transaction_id?: string;
  remarks?: string;
}

export interface OnlinePaymentRequest {
  student_id: number;
  fee_structure_id: number;
  amount: number;
  payment_gateway: string;
}

export interface FeeReportFilters {
  class_id?: number;
  fee_type?: string;
  academic_year?: string;
  payment_status?: string;
  start_date?: string;
  end_date?: string;
}

class FeeService {
  async getFeeStructures(params?: any): Promise<FeeStructure[]> {
    const response = await api.get('/fees/structures', { params });
    return response.data.data;
  }

  async createFeeStructure(feeData: CreateFeeStructureRequest): Promise<FeeStructure> {
    const response = await api.post('/fees/structures', feeData);
    return response.data.data;
  }

  async updateFeeStructure(id: number, feeData: Partial<CreateFeeStructureRequest>): Promise<FeeStructure> {
    const response = await api.put(`/fees/structures/${id}`, feeData);
    return response.data.data;
  }

  async deleteFeeStructure(id: number): Promise<void> {
    await api.delete(`/fees/structures/${id}`);
  }

  async recordPayment(paymentData: RecordPaymentRequest): Promise<FeePayment> {
    const response = await api.post('/fees/payments', paymentData);
    return response.data.data;
  }

  async processOnlinePayment(paymentData: OnlinePaymentRequest): Promise<any> {
    const response = await api.post('/fees/online-payment', paymentData);
    return response.data;
  }

  async getStudentFeeSummary(studentId: number): Promise<any> {
    const response = await api.get(`/fees/student/${studentId}/summary`);
    return response.data.data;
  }

  async getFeeCollectionReport(filters?: FeeReportFilters): Promise<any> {
    const response = await api.get('/fees/reports/collection', { params: filters });
    return response.data;
  }

  async generateReceipt(paymentId: number): Promise<Blob> {
    const response = await api.get(`/fees/payments/${paymentId}/receipt`, {
      responseType: 'blob',
    });
    return response.data;
  }

  async getFeePayments(params?: any): Promise<FeePayment[]> {
    const response = await api.get('/fees/payments', { params });
    return response.data.data;
  }

  async getOutstandingFees(params?: any): Promise<any> {
    const response = await api.get('/fees/outstanding', { params });
    return response.data;
  }
}

export default new FeeService();