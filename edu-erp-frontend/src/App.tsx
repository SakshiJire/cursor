import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import { CssBaseline } from '@mui/material';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

// Context
import { AuthProvider } from './contexts/AuthContext';

// Components
import Login from './components/Auth/Login';
import Register from './components/Auth/Register';
import DashboardLayout from './components/Layout/DashboardLayout';
import Dashboard from './components/Dashboard/Dashboard';
import StudentManagement from './components/Student/StudentManagement';
import StaffManagement from './components/Staff/StaffManagement';
import ClassManagement from './components/Class/ClassManagement';
import FeeManagement from './components/Fee/FeeManagement';
import AttendanceManagement from './components/Attendance/AttendanceManagement';
import ExamManagement from './components/Exam/ExamManagement';
import TimetableManagement from './components/Timetable/TimetableManagement';
import StudyMaterials from './components/LMS/StudyMaterials';
import LeaveManagement from './components/Leave/LeaveManagement';
import SalaryManagement from './components/Salary/SalaryManagement';
import TransportManagement from './components/Transport/TransportManagement';
import HostelManagement from './components/Hostel/HostelManagement';
import Messaging from './components/Communication/Messaging';
import Reports from './components/Reports/Reports';
import ProtectedRoute from './components/Auth/ProtectedRoute';

// Create theme
const theme = createTheme({
  palette: {
    primary: {
      main: '#1976d2',
    },
    secondary: {
      main: '#dc004e',
    },
  },
  typography: {
    fontFamily: '"Roboto", "Helvetica", "Arial", sans-serif',
  },
});

function App() {
  return (
    <ThemeProvider theme={theme}>
      <CssBaseline />
      <AuthProvider>
        <Router>
          <Routes>
            {/* Public Routes */}
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            
            {/* Protected Routes */}
            <Route path="/" element={
              <ProtectedRoute>
                <DashboardLayout />
              </ProtectedRoute>
            }>
              <Route index element={<Navigate to="/dashboard" />} />
              <Route path="dashboard" element={<Dashboard />} />
              
              {/* Student Management */}
              <Route path="students/*" element={<StudentManagement />} />
              
              {/* Staff Management */}
              <Route path="staff/*" element={<StaffManagement />} />
              
              {/* Academic Management */}
              <Route path="classes/*" element={<ClassManagement />} />
              <Route path="timetable/*" element={<TimetableManagement />} />
              
              {/* Fee Management */}
              <Route path="fees/*" element={<FeeManagement />} />
              
              {/* Attendance */}
              <Route path="attendance/*" element={<AttendanceManagement />} />
              
              {/* Examinations */}
              <Route path="exams/*" element={<ExamManagement />} />
              
              {/* Learning Management */}
              <Route path="lms/*" element={<StudyMaterials />} />
              
              {/* HR Management */}
              <Route path="leaves/*" element={<LeaveManagement />} />
              <Route path="salary/*" element={<SalaryManagement />} />
              
              {/* Facilities */}
              <Route path="transport/*" element={<TransportManagement />} />
              <Route path="hostel/*" element={<HostelManagement />} />
              
              {/* Communication */}
              <Route path="messages/*" element={<Messaging />} />
              
              {/* Reports */}
              <Route path="reports/*" element={<Reports />} />
            </Route>
            
            {/* Catch all route */}
            <Route path="*" element={<Navigate to="/dashboard" />} />
          </Routes>
        </Router>
        <ToastContainer 
          position="top-right"
          autoClose={5000}
          hideProgressBar={false}
          newestOnTop={false}
          closeOnClick
          rtl={false}
          pauseOnFocusLoss
          draggable
          pauseOnHover
        />
      </AuthProvider>
    </ThemeProvider>
  );
}

export default App;
