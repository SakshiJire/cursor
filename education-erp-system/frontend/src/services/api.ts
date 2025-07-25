import axios from 'axios';

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add auth token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle auth errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Auth APIs
export const authAPI = {
  login: (mobile: string, password: string) =>
    api.post('/auth/login', { mobile, password }),
  
  register: (userData: any) =>
    api.post('/auth/register', userData),
  
  logout: () =>
    api.post('/auth/logout'),
  
  me: () =>
    api.get('/auth/me'),
  
  sendOTP: (mobile: string) =>
    api.post('/auth/send-otp', { mobile }),
  
  verifyOTP: (mobile: string, otp: string) =>
    api.post('/auth/verify-otp', { mobile, otp }),
};

// Student APIs
export const studentAPI = {
  list: (params?: any) =>
    api.get('/students', { params }),
  
  create: (studentData: any) =>
    api.post('/students', studentData),
  
  get: (id: number) =>
    api.get(`/students/${id}`),
  
  update: (id: number, studentData: any) =>
    api.put(`/students/${id}`, studentData),
  
  deactivate: (id: number) =>
    api.delete(`/students/${id}`),
  
  uploadDocument: (id: number, formData: FormData) =>
    api.post(`/students/${id}/documents`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }),
  
  promoteStudents: (data: any) =>
    api.post('/students/promote', data),
};

// Fee APIs
export const feeAPI = {
  getStructures: (params?: any) =>
    api.get('/fees/structures', { params }),
  
  createStructure: (data: any) =>
    api.post('/fees/structures', data),
  
  updateStructure: (id: number, data: any) =>
    api.put(`/fees/structures/${id}`, data),
  
  recordPayment: (data: any) =>
    api.post('/fees/payments', data),
  
  getStudentFees: (studentId: number) =>
    api.get(`/fees/student/${studentId}`),
  
  getCollectionReport: (params?: any) =>
    api.get('/fees/collection-report', { params }),
  
  generateReceipt: (paymentId: number) =>
    api.get(`/fees/receipt/${paymentId}`),
  
  processOnlinePayment: (data: any) =>
    api.post('/fees/online-payment', data),
};

// Institute APIs
export const instituteAPI = {
  list: () =>
    api.get('/institutes'),
  
  create: (data: any) =>
    api.post('/institutes', data),
  
  get: (id: number) =>
    api.get(`/institutes/${id}`),
  
  update: (id: number, data: any) =>
    api.put(`/institutes/${id}`, data),
  
  getStats: (id: number) =>
    api.get(`/institutes/${id}/stats`),
};

// Class APIs
export const classAPI = {
  list: (params?: any) =>
    api.get('/classes', { params }),
  
  create: (data: any) =>
    api.post('/classes', data),
  
  get: (id: number) =>
    api.get(`/classes/${id}`),
  
  update: (id: number, data: any) =>
    api.put(`/classes/${id}`, data),
  
  delete: (id: number) =>
    api.delete(`/classes/${id}`),
  
  getStudents: (id: number) =>
    api.get(`/classes/${id}/students`),
};

// Subject APIs
export const subjectAPI = {
  list: (params?: any) =>
    api.get('/subjects', { params }),
  
  create: (data: any) =>
    api.post('/subjects', data),
  
  get: (id: number) =>
    api.get(`/subjects/${id}`),
  
  update: (id: number, data: any) =>
    api.put(`/subjects/${id}`, data),
  
  delete: (id: number) =>
    api.delete(`/subjects/${id}`),
};

// Attendance APIs
export const attendanceAPI = {
  markAttendance: (data: any) =>
    api.post('/attendance', data),
  
  getAttendance: (params?: any) =>
    api.get('/attendance', { params }),
  
  getStudentAttendance: (studentId: number, params?: any) =>
    api.get(`/attendance/student/${studentId}`, { params }),
  
  getClassAttendance: (classId: number, params?: any) =>
    api.get(`/attendance/class/${classId}`, { params }),
  
  getMonthlyReport: (params?: any) =>
    api.get('/attendance/monthly-report', { params }),
};

// Exam APIs
export const examAPI = {
  list: (params?: any) =>
    api.get('/exams', { params }),
  
  create: (data: any) =>
    api.post('/exams', data),
  
  get: (id: number) =>
    api.get(`/exams/${id}`),
  
  update: (id: number, data: any) =>
    api.put(`/exams/${id}`, data),
  
  delete: (id: number) =>
    api.delete(`/exams/${id}`),
  
  enterMarks: (data: any) =>
    api.post('/exams/marks', data),
  
  getResults: (examId: number, params?: any) =>
    api.get(`/exams/${examId}/results`, { params }),
  
  generateReportCard: (studentId: number, examId: number) =>
    api.get(`/exams/report-card/${studentId}/${examId}`),
};

// Timetable APIs
export const timetableAPI = {
  list: (params?: any) =>
    api.get('/timetables', { params }),
  
  create: (data: any) =>
    api.post('/timetables', data),
  
  update: (id: number, data: any) =>
    api.put(`/timetables/${id}`, data),
  
  delete: (id: number) =>
    api.delete(`/timetables/${id}`),
  
  getClassTimetable: (classId: number) =>
    api.get(`/timetables/class/${classId}`),
  
  getTeacherTimetable: (teacherId: number) =>
    api.get(`/timetables/teacher/${teacherId}`),
};

// LMS APIs
export const lmsAPI = {
  getMaterials: (params?: any) =>
    api.get('/lms/materials', { params }),
  
  uploadMaterial: (formData: FormData) =>
    api.post('/lms/materials', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }),
  
  deleteMaterial: (id: number) =>
    api.delete(`/lms/materials/${id}`),
  
  getAssignments: (params?: any) =>
    api.get('/lms/assignments', { params }),
  
  createAssignment: (data: any) =>
    api.post('/lms/assignments', data),
  
  submitAssignment: (assignmentId: number, formData: FormData) =>
    api.post(`/lms/assignments/${assignmentId}/submit`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }),
  
  getSubmissions: (assignmentId: number) =>
    api.get(`/lms/assignments/${assignmentId}/submissions`),
};

// Staff APIs
export const staffAPI = {
  list: (params?: any) =>
    api.get('/staff', { params }),
  
  create: (data: any) =>
    api.post('/staff', data),
  
  get: (id: number) =>
    api.get(`/staff/${id}`),
  
  update: (id: number, data: any) =>
    api.put(`/staff/${id}`, data),
  
  deactivate: (id: number) =>
    api.delete(`/staff/${id}`),
};

// Leave APIs
export const leaveAPI = {
  list: (params?: any) =>
    api.get('/leave', { params }),
  
  apply: (data: any) =>
    api.post('/leave', data),
  
  approve: (id: number, data: any) =>
    api.put(`/leave/${id}/approve`, data),
  
  getMyLeaves: () =>
    api.get('/leave/my-leaves'),
  
  getLeaveBalance: () =>
    api.get('/leave/balance'),
};

// Salary APIs
export const salaryAPI = {
  getStructures: (params?: any) =>
    api.get('/salary/structures', { params }),
  
  createStructure: (data: any) =>
    api.post('/salary/structures', data),
  
  processPayroll: (data: any) =>
    api.post('/salary/process-payroll', data),
  
  getPayslip: (paymentId: number) =>
    api.get(`/salary/payslip/${paymentId}`),
  
  getPayrollReport: (params?: any) =>
    api.get('/salary/payroll-report', { params }),
};

// Communication APIs
export const communicationAPI = {
  getChats: () =>
    api.get('/communication/chats'),
  
  createChat: (data: any) =>
    api.post('/communication/chats', data),
  
  getMessages: (chatId: number, params?: any) =>
    api.get(`/communication/chats/${chatId}/messages`, { params }),
  
  sendMessage: (chatId: number, data: any) =>
    api.post(`/communication/chats/${chatId}/messages`, data),
  
  markAsRead: (chatId: number) =>
    api.put(`/communication/chats/${chatId}/read`),
};

// Transport APIs
export const transportAPI = {
  getVehicles: () =>
    api.get('/transport/vehicles'),
  
  createVehicle: (data: any) =>
    api.post('/transport/vehicles', data),
  
  getRoutes: () =>
    api.get('/transport/routes'),
  
  createRoute: (data: any) =>
    api.post('/transport/routes', data),
  
  assignStudent: (data: any) =>
    api.post('/transport/assign-student', data),
  
  getTransportReport: (params?: any) =>
    api.get('/transport/report', { params }),
};

// Hostel APIs
export const hostelAPI = {
  getHostels: () =>
    api.get('/hostel/hostels'),
  
  createHostel: (data: any) =>
    api.post('/hostel/hostels', data),
  
  getRooms: (hostelId: number) =>
    api.get(`/hostel/hostels/${hostelId}/rooms`),
  
  allocateRoom: (data: any) =>
    api.post('/hostel/allocate', data),
  
  getHostelReport: (params?: any) =>
    api.get('/hostel/report', { params }),
};

// Report APIs
export const reportAPI = {
  getAdmissionStats: (params?: any) =>
    api.get('/reports/admissions', { params }),
  
  getFeeReport: (params?: any) =>
    api.get('/reports/fees', { params }),
  
  getExamReport: (params?: any) =>
    api.get('/reports/exams', { params }),
  
  getAttendanceReport: (params?: any) =>
    api.get('/reports/attendance', { params }),
  
  getTransportReport: (params?: any) =>
    api.get('/reports/transport', { params }),
  
  getHostelReport: (params?: any) =>
    api.get('/reports/hostel', { params }),
  
  getSalaryReport: (params?: any) =>
    api.get('/reports/salary', { params }),
};

// User APIs
export const userAPI = {
  list: (params?: any) =>
    api.get('/users', { params }),
  
  create: (data: any) =>
    api.post('/users', data),
  
  get: (id: number) =>
    api.get(`/users/${id}`),
  
  update: (id: number, data: any) =>
    api.put(`/users/${id}`, data),
  
  changePassword: (data: any) =>
    api.put('/users/change-password', data),
  
  updateProfile: (data: any) =>
    api.put('/users/profile', data),
};

// File APIs
export const fileAPI = {
  upload: (formData: FormData) =>
    api.post('/files/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }),
  
  download: (fileId: number) =>
    api.get(`/files/${fileId}/download`, { responseType: 'blob' }),
  
  delete: (fileId: number) =>
    api.delete(`/files/${fileId}`),
};

export default api;