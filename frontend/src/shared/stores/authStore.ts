import { create } from 'zustand';

export interface User {
  id: string;
  name: string;
  email: string;
  username: string;
  is_platform_admin: boolean;
  roles: string[];
}

export interface Company {
  id: string;
  name: string;
  code: string;
  is_platform_host: boolean;
}

export interface AuthState {
  token: string | null;
  user: User | null;
  company: Company | null;
  dashboardTarget: string | null;
  isAuthenticated: boolean;
  setAuthSession: (data: {
    token: string;
    user: User;
    company: Company | null;
    dashboard_target: string;
  }) => void;
  logout: () => void;
}

/**
 * Centralized Authentication Store (SRS_LOGIN & Zustand ^5.0)
 *
 * Persists token and user session to localStorage for immediate hydration.
 */
export const useAuthStore = create<AuthState>((set) => {
  // Initial hydration from localStorage
  const savedToken = localStorage.getItem('traceflow_token');
  const savedUser = localStorage.getItem('traceflow_user');
  const savedCompany = localStorage.getItem('traceflow_company');
  const savedTarget = localStorage.getItem('traceflow_dashboard_target');

  return {
    token: savedToken,
    user: savedUser ? JSON.parse(savedUser) : null,
    company: savedCompany ? JSON.parse(savedCompany) : null,
    dashboardTarget: savedTarget,
    isAuthenticated: Boolean(savedToken),

    setAuthSession: ({ token, user, company, dashboard_target }) => {
      localStorage.setItem('traceflow_token', token);
      localStorage.setItem('traceflow_user', JSON.stringify(user));
      if (company) {
        localStorage.setItem('traceflow_company', JSON.stringify(company));
      } else {
        localStorage.removeItem('traceflow_company');
      }
      localStorage.setItem('traceflow_dashboard_target', dashboard_target);

      set({
        token,
        user,
        company,
        dashboardTarget: dashboard_target,
        isAuthenticated: true,
      });
    },

    logout: () => {
      localStorage.removeItem('traceflow_token');
      localStorage.removeItem('traceflow_user');
      localStorage.removeItem('traceflow_company');
      localStorage.removeItem('traceflow_dashboard_target');

      set({
        token: null,
        user: null,
        company: null,
        dashboardTarget: null,
        isAuthenticated: false,
      });
    },
  };
});
