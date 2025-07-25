import React from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate, useLocation } from 'react-router-dom';

interface LayoutProps {
  children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const menuItems = [
    { path: '/dashboard', label: 'Dashboard', icon: '🏠' },
    { path: '/students', label: 'Students', icon: '👨‍🎓' },
    { path: '/students/register', label: 'Register Student', icon: '➕' },
    { path: '/fees', label: 'Fee Management', icon: '💰' },
    { path: '/academics', label: 'Academics', icon: '📚' },
    { path: '/timetable', label: 'Timetable', icon: '📅' },
    { path: '/exams', label: 'Exams & Results', icon: '📝' },
    { path: '/lms', label: 'Learning Materials', icon: '🎓' },
    { path: '/staff', label: 'Staff Management', icon: '👥' },
    { path: '/attendance', label: 'Attendance', icon: '✅' },
    { path: '/communication', label: 'Communication', icon: '💬' },
    { path: '/transport', label: 'Transport', icon: '🚌' },
    { path: '/hostel', label: 'Hostel', icon: '🏠' },
    { path: '/reports', label: 'Reports', icon: '📊' },
  ];

  // Filter menu items based on user role
  const getFilteredMenuItems = () => {
    if (!user) return [];
    
    switch (user.role) {
      case 'super_admin':
        return menuItems;
      case 'admin':
        return menuItems.filter(item => !item.path.includes('/staff') || item.path === '/staff');
      case 'teacher':
        return menuItems.filter(item => 
          ['/dashboard', '/students', '/attendance', '/exams', '/lms', '/timetable', '/communication'].includes(item.path)
        );
      case 'staff':
        return menuItems.filter(item => 
          ['/dashboard', '/students', '/fees', '/communication'].includes(item.path)
        );
      case 'student':
        return menuItems.filter(item => 
          ['/dashboard', '/lms', '/exams', '/timetable', '/communication'].includes(item.path)
        );
      case 'parent':
        return menuItems.filter(item => 
          ['/dashboard', '/students', '/fees', '/exams', '/communication'].includes(item.path)
        );
      default:
        return [{ path: '/dashboard', label: 'Dashboard', icon: '🏠' }];
    }
  };

  const filteredMenuItems = getFilteredMenuItems();

  return (
    <div className="layout">
      <div className="sidebar">
        <div className="sidebar-header">
          <h3>Education ERP</h3>
          <p>{user?.institute?.name}</p>
        </div>
        <nav className="sidebar-nav">
          {filteredMenuItems.map((item) => (
            <button
              key={item.path}
              className={`nav-item ${location.pathname === item.path ? 'active' : ''}`}
              onClick={() => navigate(item.path)}
            >
              <span className="nav-icon">{item.icon}</span>
              <span className="nav-label">{item.label}</span>
            </button>
          ))}
        </nav>
        <div className="sidebar-footer">
          <div className="user-info">
            <div className="user-avatar">
              {user?.first_name?.charAt(0)}{user?.last_name?.charAt(0)}
            </div>
            <div className="user-details">
              <div className="user-name">{user?.first_name} {user?.last_name}</div>
              <div className="user-role">{user?.role?.replace('_', ' ').toUpperCase()}</div>
            </div>
          </div>
          <button className="btn btn-secondary logout-btn" onClick={handleLogout}>
            Logout
          </button>
        </div>
      </div>
      <div className="main-content">
        {children}
      </div>
    </div>
  );
};

export default Layout;