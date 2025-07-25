import React from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';

interface HeaderProps {
  title?: string;
  showBackButton?: boolean;
}

const Header: React.FC<HeaderProps> = ({ title, showBackButton = false }) => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const handleBack = () => {
    navigate(-1);
  };

  return (
    <div className="header">
      <div className="header-left">
        {showBackButton && (
          <button className="btn btn-secondary" onClick={handleBack}>
            ← Back
          </button>
        )}
        {title && <h1>{title}</h1>}
      </div>
      
      <div className="header-center">
        <h2>Education ERP</h2>
      </div>

      <div className="header-right">
        {user && (
          <div className="user-info">
            <div className="user-details">
              <span className="user-name">{user.first_name} {user.last_name}</span>
              <span className="user-role">{user.role}</span>
              {user.institute && (
                <span className="institute-name">{user.institute.name}</span>
              )}
            </div>
            {user.profile_image && (
              <img 
                src={user.profile_image} 
                alt="Profile" 
                className="user-avatar"
              />
            )}
            <button className="btn btn-outline" onClick={handleLogout}>
              Logout
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default Header;