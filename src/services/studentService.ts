import api from './api';

export interface Student {
  id: number;
  user_id: number;
  institute_id: number;
  class_id: number;
  admission_number: string;
  roll_number?: string;
  date_of_admission: string;
  academic_year: string;
  student_type: string;
  blood_group?: string;
  religion?: string;
  caste?: string;
  category?: string;
  previous_school?: string;
  transport_required: boolean;
  hostel_required: boolean;
  documents?: string;
  status: string;
  user: {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    mobile: string;
    date_of_birth: string;
    gender: string;
    address: string;
    city: string;
    state: string;
    pincode: string;
    profile_image?: string;
  };
  class?: {
    id: number;
    name: string;
    section: string;
  };
  parent?: {
    id: number;
    father_name: string;
    mother_name: string;
    father_occupation: string;
    mother_occupation: string;
    father_mobile: string;
    mother_mobile: string;
    father_email?: string;
    mother_email?: string;
    annual_income?: number;
    address: string;
  };
}

export interface CreateStudentRequest {
  // Student User Info
  first_name: string;
  last_name: string;
  email: string;
  mobile: string;
  date_of_birth: string;
  gender: string;
  address: string;
  city: string;
  state: string;
  pincode: string;
  
  // Student Specific Info
  class_id: number;
  admission_number?: string;
  roll_number?: string;
  date_of_admission: string;
  academic_year: string;
  student_type: string;
  blood_group?: string;
  religion?: string;
  caste?: string;
  category?: string;
  previous_school?: string;
  transport_required: boolean;
  hostel_required: boolean;
  
  // Parent Info
  father_name: string;
  mother_name: string;
  father_occupation: string;
  mother_occupation: string;
  father_mobile: string;
  mother_mobile: string;
  father_email?: string;
  mother_email?: string;
  annual_income?: number;
  parent_address: string;
}

export interface StudentFilters {
  class_id?: number;
  status?: string;
  academic_year?: string;
  search?: string;
  page?: number;
  per_page?: number;
}

class StudentService {
  async getStudents(filters?: StudentFilters): Promise<any> {
    const response = await api.get('/students', { params: filters });
    return response.data;
  }

  async getStudent(id: number): Promise<Student> {
    const response = await api.get(`/students/${id}`);
    return response.data.data;
  }

  async createStudent(studentData: CreateStudentRequest): Promise<Student> {
    const response = await api.post('/students', studentData);
    return response.data.data;
  }

  async updateStudent(id: number, studentData: Partial<CreateStudentRequest>): Promise<Student> {
    const response = await api.put(`/students/${id}`, studentData);
    return response.data.data;
  }

  async deactivateStudent(id: number): Promise<void> {
    await api.patch(`/students/${id}/deactivate`);
  }

  async uploadDocument(studentId: number, file: File, type: string): Promise<any> {
    const formData = new FormData();
    formData.append('document', file);
    formData.append('type', type);
    
    const response = await api.post(`/students/${studentId}/documents`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }

  async promoteStudents(currentClassId: number, newClassId: number, studentIds: number[]): Promise<any> {
    const response = await api.post('/students/bulk-promote', {
      current_class_id: currentClassId,
      new_class_id: newClassId,
      student_ids: studentIds,
    });
    return response.data;
  }

  async getStudentsByClass(classId: number): Promise<Student[]> {
    const response = await api.get(`/students/by-class/${classId}`);
    return response.data.data;
  }
}

export default new StudentService();