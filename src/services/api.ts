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

// Request interceptor to add auth token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor for error handling
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

// Auth API
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

// Student API
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
    api.patch(`/students/${id}/deactivate`),
  uploadDocument: (id: number, formData: FormData) =>
    api.post(`/students/${id}/documents`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  promote: (promotionData: any) =>
    api.post('/students/bulk-promote', promotionData),
};

// Fee API
export const feeAPI = {
  getStructures: (params?: any) =>
    api.get('/fees/structures', { params }),
  createStructure: (structureData: any) =>
    api.post('/fees/structures', structureData),
  updateStructure: (id: number, structureData: any) =>
    api.put(`/fees/structures/${id}`, structureData),
  recordPayment: (paymentData: any) =>
    api.post('/fees/payments', paymentData),
  getStudentFees: (studentId: number) =>
    api.get(`/fees/student/${studentId}`),
  getPaymentHistory: (params?: any) =>
    api.get('/fees/payments', { params }),
  generateReceipt: (paymentId: number) =>
    api.get(`/fees/payments/${paymentId}/receipt`),
  getCollectionReport: (params: any) =>
    api.get('/fees/reports/collection', { params }),
  processOnlinePayment: (paymentData: any) =>
    api.post('/fees/payments/online', paymentData),
};

// Institute API
export const instituteAPI = {
  list: () =>
    api.get('/institutes'),
  create: (instituteData: any) =>
    api.post('/institutes', instituteData),
  get: (id: number) =>
    api.get(`/institutes/${id}`),
  update: (id: number, instituteData: any) =>
    api.put(`/institutes/${id}`, instituteData),
  delete: (id: number) =>
    api.delete(`/institutes/${id}`),
};

// Class API
export const classAPI = {
  list: (params?: any) =>
    api.get('/classes', { params }),
  create: (classData: any) =>
    api.post('/classes', classData),
  get: (id: number) =>
    api.get(`/classes/${id}`),
  update: (id: number, classData: any) =>
    api.put(`/classes/${id}`, classData),
  delete: (id: number) =>
    api.delete(`/classes/${id}`),
};

// Subject API
export const subjectAPI = {
  list: (params?: any) =>
    api.get('/subjects', { params }),
  create: (subjectData: any) =>
    api.post('/subjects', subjectData),
  get: (id: number) =>
    api.get(`/subjects/${id}`),
  update: (id: number, subjectData: any) =>
    api.put(`/subjects/${id}`, subjectData),
  delete: (id: number) =>
    api.delete(`/subjects/${id}`),
  assignToClass: (assignmentData: any) =>
    api.post('/subjects/assign', assignmentData),
};

// Attendance API
export const attendanceAPI = {
  mark: (attendanceData: any) =>
    api.post('/attendance', attendanceData),
  getByDate: (date: string, params?: any) =>
    api.get('/attendance', { params: { date, ...params } }),
  getStudentAttendance: (studentId: number, params?: any) =>
    api.get(`/attendance/student/${studentId}`, { params }),
  getClassAttendance: (classId: number, params?: any) =>
    api.get(`/attendance/class/${classId}`, { params }),
  getMonthlyReport: (params: any) =>
    api.get('/attendance/reports/monthly', { params }),
};

// Exam API
export const examAPI = {
  list: (params?: any) =>
    api.get('/exams', { params }),
  create: (examData: any) =>
    api.post('/exams', examData),
  get: (id: number) =>
    api.get(`/exams/${id}`),
  update: (id: number, examData: any) =>
    api.put(`/exams/${id}`, examData),
  delete: (id: number) =>
    api.delete(`/exams/${id}`),
  enterMarks: (marksData: any) =>
    api.post('/exam-results', marksData),
  getResults: (examId: number, params?: any) =>
    api.get(`/exams/${examId}/results`, { params }),
  getStudentResults: (studentId: number, params?: any) =>
    api.get(`/exam-results/student/${studentId}`, { params }),
  generateReportCard: (studentId: number, examId: number) =>
    api.get(`/exam-results/report-card/${studentId}/${examId}`),
};

// Timetable API
export const timetableAPI = {
  get: (params?: any) =>
    api.get('/timetables', { params }),
  create: (timetableData: any) =>
    api.post('/timetables', timetableData),
  update: (id: number, timetableData: any) =>
    api.put(`/timetables/${id}`, timetableData),
  delete: (id: number) =>
    api.delete(`/timetables/${id}`),
  getByClass: (classId: number) =>
    api.get(`/timetables/class/${classId}`),
  getByTeacher: (teacherId: number) =>
    api.get(`/timetables/teacher/${teacherId}`),
};

// LMS API
export const lmsAPI = {
  getMaterials: (params?: any) =>
    api.get('/learning-materials', { params }),
  uploadMaterial: (formData: FormData) =>
    api.post('/learning-materials', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  deleteMaterial: (id: number) =>
    api.delete(`/learning-materials/${id}`),
  getAssignments: (params?: any) =>
    api.get('/assignments', { params }),
  createAssignment: (assignmentData: any) =>
    api.post('/assignments', assignmentData),
  submitAssignment: (assignmentId: number, formData: FormData) =>
    api.post(`/assignments/${assignmentId}/submit`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  getSubmissions: (assignmentId: number) =>
    api.get(`/assignments/${assignmentId}/submissions`),
  gradeSubmission: (submissionId: number, gradeData: any) =>
    api.put(`/assignment-submissions/${submissionId}/grade`, gradeData),
};

// Staff API
export const staffAPI = {
  list: (params?: any) =>
    api.get('/staff', { params }),
  create: (staffData: any) =>
    api.post('/staff', staffData),
  get: (id: number) =>
    api.get(`/staff/${id}`),
  update: (id: number, staffData: any) =>
    api.put(`/staff/${id}`, staffData),
  deactivate: (id: number) =>
    api.patch(`/staff/${id}/deactivate`),
};

// Leave API
export const leaveAPI = {
  list: (params?: any) =>
    api.get('/leaves', { params }),
  apply: (leaveData: any) =>
    api.post('/leaves', leaveData),
  approve: (id: number, action: 'approve' | 'reject', comments?: string) =>
    api.patch(`/leaves/${id}/${action}`, { comments }),
  getBalance: (userId: number) =>
    api.get(`/leaves/balance/${userId}`),
};

// Salary API
export const salaryAPI = {
  getStructures: (params?: any) =>
    api.get('/salary-structures', { params }),
  createStructure: (structureData: any) =>
    api.post('/salary-structures', structureData),
  processPayroll: (payrollData: any) =>
    api.post('/salary-payments/process', payrollData),
  getPayments: (params?: any) =>
    api.get('/salary-payments', { params }),
  generatePayslip: (paymentId: number) =>
    api.get(`/salary-payments/${paymentId}/payslip`),
};

// Communication API
export const communicationAPI = {
  getChats: () =>
    api.get('/chats'),
  createChat: (chatData: any) =>
    api.post('/chats', chatData),
  getMessages: (chatId: number, params?: any) =>
    api.get(`/chats/${chatId}/messages`, { params }),
  sendMessage: (chatId: number, messageData: any) =>
    api.post(`/chats/${chatId}/messages`, messageData),
  markAsRead: (chatId: number) =>
    api.patch(`/chats/${chatId}/read`),
};

// Transport API
export const transportAPI = {
  getVehicles: () =>
    api.get('/transport/vehicles'),
  createVehicle: (vehicleData: any) =>
    api.post('/transport/vehicles', vehicleData),
  getRoutes: () =>
    api.get('/transport/routes'),
  createRoute: (routeData: any) =>
    api.post('/transport/routes', routeData),
  assignStudent: (assignmentData: any) =>
    api.post('/transport/assign-student', assignmentData),
  getAssignments: (params?: any) =>
    api.get('/transport/assignments', { params }),
  getFeeReport: (params: any) =>
    api.get('/transport/reports/fees', { params }),
};

// Hostel API
export const hostelAPI = {
  getHostels: () =>
    api.get('/hostels'),
  createHostel: (hostelData: any) =>
    api.post('/hostels', hostelData),
  getRooms: (hostelId?: number) =>
    api.get('/hostel-rooms', { params: { hostel_id: hostelId } }),
  createRoom: (roomData: any) =>
    api.post('/hostel-rooms', roomData),
  allocateStudent: (allocationData: any) =>
    api.post('/student-hostel', allocationData),
  getAllocations: (params?: any) =>
    api.get('/student-hostel', { params }),
  markAttendance: (attendanceData: any) =>
    api.post('/hostel-attendance', attendanceData),
};

// Reports API
export const reportsAPI = {
  getAdmissionStats: (params: any) =>
    api.get('/reports/admissions', { params }),
  getFeeCollection: (params: any) =>
    api.get('/reports/fees', { params }),
  getExamPerformance: (params: any) =>
    api.get('/reports/exam-performance', { params }),
  getTransportSummary: (params: any) =>
    api.get('/reports/transport', { params }),
  getHostelSummary: (params: any) =>
    api.get('/reports/hostel', { params }),
  getStaffSalary: (params: any) =>
    api.get('/reports/staff-salary', { params }),
  getStaffLeave: (params: any) =>
    api.get('/reports/staff-leave', { params }),
};

// User API
export const userAPI = {
  list: (params?: any) =>
    api.get('/users', { params }),
  create: (userData: any) =>
    api.post('/users', userData),
  get: (id: number) =>
    api.get(`/users/${id}`),
  update: (id: number, userData: any) =>
    api.put(`/users/${id}`, userData),
  updatePassword: (id: number, passwordData: any) =>
    api.put(`/users/${id}/password`, passwordData),
  deactivate: (id: number) =>
    api.patch(`/users/${id}/deactivate`),
};

// File API
export const fileAPI = {
  upload: (formData: FormData, type: string) =>
    api.post(`/files/upload?type=${type}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  download: (filename: string) =>
    api.get(`/files/download/${filename}`, { responseType: 'blob' }),
  delete: (filename: string) =>
    api.delete(`/files/${filename}`),
};

export default api;