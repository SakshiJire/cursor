import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

// Types
interface User {
  id: number;
  first_name: string;
  last_name: string;
  email?: string;
  mobile: string;
  user_type: 'admin' | 'student' | 'parent' | 'staff' | 'teacher';
  avatar?: string;
  institution?: {
    id: number;
    name: string;
    type: string;
  };
  student?: any;
  staff?: any;
  children?: any[];
}

interface AuthContextType {
  user: User | null;
  token: string | null;
  login: (mobile: string, password: string) => Promise<boolean>;
  logout: () => void;
  register: (userData: any) => Promise<boolean>;
  updateProfile: (data: any) => Promise<boolean>;
  isLoading: boolean;
}

// API Configuration
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

axios.defaults.baseURL = API_BASE_URL;

// Create context
const AuthContext = createContext<AuthContextType | undefined>(undefined);

// Auth Provider Props
interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(localStorage.getItem('token'));
  const [isLoading, setIsLoading] = useState(true);

  // Set axios default authorization header
  useEffect(() => {
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    } else {
      delete axios.defaults.headers.common['Authorization'];
    }
  }, [token]);

  // Load user profile on app start
  useEffect(() => {
    if (token) {
      loadUserProfile();
    } else {
      setIsLoading(false);
    }
  }, [token]);

  const loadUserProfile = async () => {
    try {
      const response = await axios.get('/auth/profile');
      if (response.data.success) {
        setUser(response.data.data);
      }
    } catch (error) {
      console.error('Failed to load user profile:', error);
      logout();
    } finally {
      setIsLoading(false);
    }
  };

  const login = async (mobile: string, password: string): Promise<boolean> => {
    try {
      setIsLoading(true);
      const response = await axios.post('/auth/login', { mobile, password });
      
      if (response.data.success) {
        const { user, token } = response.data.data;
        setUser(user);
        setToken(token);
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        toast.success('Login successful!');
        return true;
      }
      return false;
    } catch (error: any) {
      const message = error.response?.data?.message || 'Login failed';
      toast.error(message);
      return false;
    } finally {
      setIsLoading(false);
    }
  };

  const register = async (userData: any): Promise<boolean> => {
    try {
      setIsLoading(true);
      const response = await axios.post('/auth/register', userData);
      
      if (response.data.success) {
        const { user, token } = response.data.data;
        setUser(user);
        setToken(token);
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        toast.success('Registration successful!');
        return true;
      }
      return false;
    } catch (error: any) {
      const message = error.response?.data?.message || 'Registration failed';
      toast.error(message);
      return false;
    } finally {
      setIsLoading(false);
    }
  };

  const updateProfile = async (data: any): Promise<boolean> => {
    try {
      const response = await axios.put('/auth/profile', data);
      
      if (response.data.success) {
        setUser(response.data.data);
        toast.success('Profile updated successfully!');
        return true;
      }
      return false;
    } catch (error: any) {
      const message = error.response?.data?.message || 'Profile update failed';
      toast.error(message);
      return false;
    }
  };

  const logout = () => {
    setUser(null);
    setToken(null);
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];
    toast.info('Logged out successfully');
  };

  const value: AuthContextType = {
    user,
    token,
    login,
    logout,
    register,
    updateProfile,
    isLoading,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};

// Custom hook to use auth context
export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};