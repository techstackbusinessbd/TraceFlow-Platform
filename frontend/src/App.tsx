import React, { useEffect, useState } from 'react';
import { useAuthStore } from './shared/stores/authStore';
import { LoginPage } from './features/auth/pages/LoginPage';
import { DashboardShell } from './features/dashboard/DashboardShell';
import { apiClient } from './shared/api/client';

export const App: React.FC = () => {
  const { isAuthenticated, setAuthSession, logout } = useAuthStore();
  const [isVerifying, setIsVerifying] = useState(true);

  // Validate session on mount if token is present in localStorage
  useEffect(() => {
    const verifySession = async () => {
      if (!isAuthenticated) {
        setIsVerifying(false);
        return;
      }

      try {
        const response = await apiClient.get('/auth/me');
        if (response.data.success && response.data.data) {
          const currentToken = useAuthStore.getState().token;
          if (currentToken) {
            setAuthSession({
              token: currentToken,
              user: response.data.data.user,
              company: response.data.data.company,
              dashboard_target: response.data.data.dashboard_target,
            });
          }
        }
      } catch {
        // If /me fails (e.g. 401 expired), clear session
        logout();
      } finally {
        setIsVerifying(false);
      }
    };

    verifySession();
  }, [isAuthenticated, setAuthSession, logout]);

  if (isVerifying) {
    return (
      <div className="min-h-screen w-full flex items-center justify-center bg-bg-app text-text-muted text-xs">
        <span className="w-5 h-5 border-2 border-brand-accent/30 border-t-brand-accent rounded-full animate-spin mr-2" />
        Verifying secure session...
      </div>
    );
  }

  return isAuthenticated ? <DashboardShell /> : <LoginPage />;
};

export default App;
