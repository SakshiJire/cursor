import React from 'react';

interface LoadingSpinnerProps {
  size?: 'small' | 'medium' | 'large';
  message?: string;
}

const LoadingSpinner: React.FC<LoadingSpinnerProps> = ({ 
  size = 'medium', 
  message = 'Loading...' 
}) => {
  return (
    <div className={`spinner-container ${size}`}>
      <div className={`spinner ${size}`}></div>
      {message && <p className="spinner-message">{message}</p>}
    </div>
  );
};

export default LoadingSpinner;