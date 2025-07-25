import React from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';

const Dashboard: React.FC = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  if (!user) {
    return <div>Loading...</div>;
  }

  return (
    <div className="dashboard">
      <div className="header">
        <div className="container flex justify-between align-center">
          <h1>Education ERP System</h1>
          <div className="flex align-center gap-4">
            <span>Welcome, {user.name}</span>
            <button onClick={handleLogout} className="btn btn-secondary">
              Logout
            </button>
          </div>
        </div>
      </div>

      <div className="container">
        <div className="dashboard-grid">
          <div className="dashboard-card">
            <h3>Total Students</h3>
            <div className="number">156</div>
          </div>
          <div className="dashboard-card">
            <h3>Total Teachers</h3>
            <div className="number">24</div>
          </div>
          <div className="dashboard-card">
            <h3>This Month Fees</h3>
            <div className="number">₹2,45,000</div>
          </div>
          <div className="dashboard-card">
            <h3>Pending Fees</h3>
            <div className="number">₹45,000</div>
          </div>
        </div>

        <div className="card">
          <div className="card-header">
            Quick Actions
          </div>
          <div className="card-body">
            <div className="flex gap-4">
              <button 
                onClick={() => navigate('/students/register')}
                className="btn btn-primary"
              >
                Register Student
              </button>
              <button 
                onClick={() => navigate('/fees')}
                className="btn btn-success"
              >
                Fee Management
              </button>
              <button className="btn btn-secondary">
                View Reports
              </button>
            </div>
          </div>
        </div>

        <div className="card">
          <div className="card-header">
            Recent Activities
          </div>
          <div className="card-body">
            <table className="table">
              <thead>
                <tr>
                  <th>Activity</th>
                  <th>User</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>New student registration</td>
                  <td>Admin</td>
                  <td>2024-03-15</td>
                  <td>Completed</td>
                </tr>
                <tr>
                  <td>Fee payment recorded</td>
                  <td>Cashier</td>
                  <td>2024-03-15</td>
                  <td>Completed</td>
                </tr>
                <tr>
                  <td>Exam results published</td>
                  <td>Teacher</td>
                  <td>2024-03-14</td>
                  <td>Completed</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;