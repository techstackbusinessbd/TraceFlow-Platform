import React, { useState } from 'react';
import { User, Lock, Eye, EyeOff, ArrowRight, ShieldCheck, Cpu } from 'lucide-react';
import { Button, Input, Card, Alert } from '../../../shared/components/ui';
import { apiClient } from '../../../shared/api/client';
import { useAuthStore } from '../../../shared/stores/authStore';

interface LoginResponse {
  success: boolean;
  message: string;
  data: {
    token: string;
    user: {
      id: string;
      name: string;
      email: string;
      username: string;
      is_platform_admin: boolean;
      roles: string[];
    };
    company: {
      id: string;
      name: string;
      code: string;
      is_platform_host: boolean;
    } | null;
    dashboard_target: string;
  };
}

export const LoginPage: React.FC = () => {
  const [login, setLogin] = useState('');
  const [password, setPassword] = useState('');
  const [remember, setRemember] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [fieldErrors, setFieldErrors] = useState<{ login?: string; password?: string }>({});

  const setAuthSession = useAuthStore((state) => state.setAuthSession);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMessage(null);
    setFieldErrors({});

    // Client-side quick validation
    const errors: { login?: string; password?: string } = {};
    if (!login.trim()) {
      errors.login = 'Please provide your email address or username.';
    }
    if (!password) {
      errors.password = 'Password is required.';
    }

    if (Object.keys(errors).length > 0) {
      setFieldErrors(errors);
      return;
    }

    setIsLoading(true);

    try {
      const response = await apiClient.post<LoginResponse>('/auth/login', {
        login: login.trim(),
        password,
        device_name: 'TraceFlow-Enterprise-Browser',
        remember,
      });

      if (response.data.success && response.data.data) {
        setAuthSession(response.data.data);
      }
    } catch (err: unknown) {
      if (err && typeof err === 'object' && 'response' in err) {
        const axiosErr = err as {
          response?: {
            status: number;
            data?: {
              message?: string;
              errors?: Record<string, string[]>;
            };
          };
        };

        if (axiosErr.response?.data?.errors) {
          const apiErrors = axiosErr.response.data.errors;
          setFieldErrors({
            login: apiErrors.login?.[0] || apiErrors.email?.[0],
            password: apiErrors.password?.[0],
          });
        }

        setErrorMessage(
          axiosErr.response?.data?.message ||
            'These credentials do not match our records. Please try again.'
        );
      } else {
        setErrorMessage('Unable to connect to TraceFlow backend. Please check connection.');
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleQuickFill = (quickLogin: string, quickPass: string) => {
    setLogin(quickLogin);
    setPassword(quickPass);
    setErrorMessage(null);
    setFieldErrors({});
  };

  return (
    <div className="min-h-screen w-full flex flex-col items-center justify-center bg-bg-app p-4 antialiased selection:bg-brand-accent selection:text-white">
      {/* Brand Header */}
      <div className="flex flex-col items-center mb-6 text-center">
        <div className="w-12 h-12 rounded-md bg-brand-primary flex items-center justify-center text-white shadow-card mb-3">
          <Cpu className="w-6 h-6 text-brand-accent" />
        </div>
        <h1 className="text-2xl font-bold tracking-tight text-text-primary">
          TraceFlow<span className="text-brand-accent font-semibold">.Platform</span>
        </h1>
        <p className="text-xs text-text-muted mt-1 font-medium">
          Garments & Enterprise Multi-Tenant Core Engine
        </p>
      </div>

      {/* Main Authentication Card */}
      <Card className="w-full max-w-md p-0 overflow-hidden border border-border-default shadow-card bg-bg-surface">
        <div className="p-6 sm:p-8">
          <div className="mb-6">
            <h2 className="text-lg font-semibold text-text-primary">Sign in to your account</h2>
            <p className="text-xs text-text-muted mt-0.5">
              Enter your corporate credentials to access the workspace.
            </p>
          </div>

          {/* Top-Level Error Alert */}
          {errorMessage && (
            <Alert variant="danger" className="mb-5">
              {errorMessage}
            </Alert>
          )}

          {/* Form */}
          <form noValidate onSubmit={handleSubmit} className="flex flex-col gap-4">
            <Input
              label="Email or Username"
              type="text"
              id="login-field"
              placeholder="e.g. superadmin or operator@company.com"
              value={login}
              onChange={(e) => setLogin(e.target.value)}
              error={fieldErrors.login}
              leftIcon={<User className="w-4 h-4" />}
              autoComplete="username"
              disabled={isLoading}
            />

            <Input
              label="Password"
              type={showPassword ? 'text' : 'password'}
              id="password-field"
              placeholder="••••••••••••"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              error={fieldErrors.password}
              leftIcon={<Lock className="w-4 h-4" />}
              rightIcon={
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="p-1 hover:text-text-primary transition-colors focus:outline-none"
                  aria-label={showPassword ? 'Hide password' : 'Show password'}
                  tabIndex={-1}
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              }
              autoComplete="current-password"
              disabled={isLoading}
            />

            <div className="flex items-center justify-between text-xs pt-1">
              <label className="flex items-center gap-2 cursor-pointer select-none text-text-secondary font-medium">
                <input
                  type="checkbox"
                  checked={remember}
                  onChange={(e) => setRemember(e.target.checked)}
                  className="rounded-xs border-border-strong text-brand-accent focus:ring-brand-accent/20 h-3.5 w-3.5"
                />
                Remember device for 30 days
              </label>

              <a
                href="#forgot-password"
                onClick={(e) => {
                  e.preventDefault();
                  alert('Please contact your factory system administrator to reset password.');
                }}
                className="text-brand-accent hover:text-brand-accent-hover font-medium transition-colors"
              >
                Forgot password?
              </a>
            </div>

            <Button
              type="submit"
              variant="primary"
              size="default"
              isLoading={isLoading}
              rightIcon={<ArrowRight className="w-4 h-4" />}
              className="mt-2 w-full h-11"
            >
              Sign In to Platform
            </Button>
          </form>

          {/* Quick Demo Switcher (SRS_LOGIN Section 2.5) */}
          <div className="mt-8 pt-6 border-t border-border-subtle">
            <span className="block text-[11px] font-semibold text-text-muted uppercase tracking-wider mb-2.5">
              Evaluation Quick-Fill Access
            </span>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
              <button
                type="button"
                onClick={() => handleQuickFill('superadmin', 'TraceFlow@2026!Root')}
                className="flex flex-col items-start p-2.5 rounded-sm bg-slate-50 border border-slate-200 hover:bg-blue-50/70 hover:border-brand-accent/40 transition-all text-left"
              >
                <div className="flex items-center gap-1.5 font-medium text-text-primary">
                  <ShieldCheck className="w-3.5 h-3.5 text-brand-accent" />
                  <span>Superadmin</span>
                </div>
                <span className="text-[10px] text-text-muted font-mono mt-0.5">superadmin</span>
              </button>

              <button
                type="button"
                onClick={() => handleQuickFill('backend.team', 'TraceFlow@2026!Root')}
                className="flex flex-col items-start p-2.5 rounded-sm bg-slate-50 border border-slate-200 hover:bg-blue-50/70 hover:border-brand-accent/40 transition-all text-left"
              >
                <div className="flex items-center gap-1.5 font-medium text-text-primary">
                  <Cpu className="w-3.5 h-3.5 text-slate-700" />
                  <span>Backend Squad</span>
                </div>
                <span className="text-[10px] text-text-muted font-mono mt-0.5">backend.team</span>
              </button>
            </div>
          </div>
        </div>

        {/* Card Footer (SRS_LOGIN Section 2.6) */}
        <div className="bg-slate-50/90 border-t border-slate-100 py-2.5 px-6 flex items-center justify-between text-[11px] text-text-muted">
          <span>Hardware Protected • ISO 27001</span>
          <span className="font-mono">TraceFlow-Platform v1.0</span>
        </div>
      </Card>
    </div>
  );
};
