import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import StudentRegistration from './pages/StudentRegistration';
import FeeManagement from './pages/FeeManagement';
import './App.css';

function App() {
  return (
    <div className="App">
      <AuthProvider>
        <Router>
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/dashboard" element={<Dashboard />} />
            <Route path="/students/register" element={<StudentRegistration />} />
            <Route path="/fees" element={<FeeManagement />} />
            <Route path="/" element={<Navigate to="/login" replace />} />
          </Routes>
        </Router>
      </AuthProvider>
    </div>
  );
}

export default App;
