import api from './api';

export interface LoginRequest {
  mobile: string;
  password: string;
}

export interface RegisterRequest {
  institute_id: number;
  first_name: string;
  last_name: string;
  email: string;
  mobile: string;
  password: string;
  role: string;
}

export interface User {
  id: number;
  institute_id: number;
  first_name: string;
  last_name: string;
  email: string;
  mobile: string;
  role: string;
  employee_id?: string;
  profile_image?: string;
  status: string;
  institute?: {
    id: number;
    name: string;
    type: string;
    code: string;
  };
}

export interface AuthResponse {
  success: boolean;
  message: string;
  data: {
    user: User;
    token: string;
    institute?: any;
  };
}

class AuthService {
  async login(credentials: LoginRequest): Promise<AuthResponse> {
    const response = await api.post('/auth/login', credentials);
    return response.data;
  }

  async register(userData: RegisterRequest): Promise<AuthResponse> {
    const response = await api.post('/auth/register', userData);
    return response.data;
  }

  async logout(): Promise<void> {
    try {
      await api.post('/auth/logout');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      localStorage.removeItem('token');
    }
  }

  async getMe(): Promise<User> {
    const response = await api.get('/auth/me');
    return response.data.data;
  }

  async sendOTP(mobile: string): Promise<any> {
    const response = await api.post('/auth/send-otp', { mobile });
    return response.data;
  }

  async verifyOTP(mobile: string, otp: string): Promise<any> {
    const response = await api.post('/auth/verify-otp', { mobile, otp });
    return response.data;
  }
}

export default new AuthService();