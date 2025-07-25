import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

interface MenuItem {
  title: string;
  path: string;
  icon: string;
  roles: string[];
}

const menuItems: MenuItem[] = [
  { title: 'Dashboard', path: '/dashboard', icon: '🏠', roles: ['super_admin', 'admin', 'staff', 'teacher', 'student', 'parent'] },
  { title: 'Student Registration', path: '/students/register', icon: '👤', roles: ['super_admin', 'admin', 'staff'] },
  { title: 'Student Management', path: '/students', icon: '👥', roles: ['super_admin', 'admin', 'staff', 'teacher'] },
  { title: 'Fee Management', path: '/fees', icon: '💰', roles: ['super_admin', 'admin', 'staff'] },
  { title: 'Fee Payments', path: '/fees/payments', icon: '💳', roles: ['super_admin', 'admin', 'staff'] },
  { title: 'Classes', path: '/classes', icon: '🏫', roles: ['super_admin', 'admin', 'staff'] },
  { title: 'Subjects', path: '/subjects', icon: '📚', roles: ['super_admin', 'admin', 'staff', 'teacher'] },
  { title: 'Timetable', path: '/timetable', icon: '📅', roles: ['super_admin', 'admin', 'staff', 'teacher', 'student'] },
  { title: 'Attendance', path: '/attendance', icon: '✅', roles: ['super_admin', 'admin', 'staff', 'teacher', 'student'] },
  { title: 'Exams', path: '/exams', icon: '📝', roles: ['super_admin', 'admin', 'staff', 'teacher', 'student'] },
  { title: 'Results', path: '/results', icon: '🏆', roles: ['super_admin', 'admin', 'staff', 'teacher', 'student', 'parent'] },
  { title: 'Learning Materials', path: '/lms', icon: '📖', roles: ['super_admin', 'admin', 'staff', 'teacher', 'student'] },
  { title: 'Staff Management', path: '/staff', icon: '👨‍💼', roles: ['super_admin', 'admin'] },
  { title: 'Leave Management', path: '/leave', icon: '🏖️', roles: ['super_admin', 'admin', 'staff', 'teacher'] },
  { title: 'Salary Management', path: '/salary', icon: '💵', roles: ['super_admin', 'admin'] },
  { title: 'Communication', path: '/communication', icon: '💬', roles: ['super_admin', 'admin', 'staff', 'teacher', 'student', 'parent'] },
  { title: 'Transport', path: '/transport', icon: '🚌', roles: ['super_admin', 'admin', 'staff'] },
  { title: 'Hostel', path: '/hostel', icon: '🏠', roles: ['super_admin', 'admin', 'staff'] },
  { title: 'Reports', path: '/reports', icon: '📊', roles: ['super_admin', 'admin', 'staff'] },
  { title: 'Institutes', path: '/institutes', icon: '🏢', roles: ['super_admin'] },
];

const Sidebar: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { user } = useAuth();

  if (!user) return null;

  const filteredMenuItems = menuItems.filter(item => 
    item.roles.includes(user.role)
  );

  const handleNavigation = (path: string) => {
    navigate(path);
  };

  return (
    <div className="sidebar">
      <div className="sidebar-header">
        <h3>Menu</h3>
      </div>
      
      <nav className="nav">
        <ul className="nav-list">
          {filteredMenuItems.map((item) => (
            <li key={item.path} className="nav-item">
              <button
                className={`nav-link ${location.pathname === item.path ? 'active' : ''}`}
                onClick={() => handleNavigation(item.path)}
              >
                <span className="nav-icon">{item.icon}</span>
                <span className="nav-title">{item.title}</span>
              </button>
            </li>
          ))}
        </ul>
      </nav>
      
      <div className="sidebar-footer">
        <div className="user-info-sidebar">
          <p className="institute-info">
            {user.institute?.name} ({user.institute?.type})
          </p>
        </div>
      </div>
    </div>
  );
};

export default Sidebar;