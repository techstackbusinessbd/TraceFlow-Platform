import React from 'react';
import { LogOut, LayoutDashboard, Building2, Shield, User as UserIcon } from 'lucide-react';
import { useAuthStore } from '../../shared/stores/authStore';
import { Button, Card } from '../../shared/components/ui';
import { apiClient } from '../../shared/api/client';

export const DashboardShell: React.FC = () => {
  const { user, company, dashboardTarget, logout } = useAuthStore();

  const handleLogout = async () => {
    try {
      await apiClient.post('/auth/logout');
    } catch {
      // Ignore network errors on logout
    } finally {
      logout();
    }
  };

  const isPlatformHost = dashboardTarget === '/platform/command-center';

  return (
    <div className="min-h-screen bg-bg-app flex flex-col text-text-primary">
      {/* Top Navbar */}
      <header className="h-14 bg-bg-surface border-b border-border-subtle px-6 flex items-center justify-between shadow-xs">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-sm bg-brand-primary flex items-center justify-center text-white font-bold text-sm">
            TF
          </div>
          <span className="font-semibold text-sm tracking-tight text-text-primary">
            TraceFlow<span className="text-brand-accent">.Platform</span>
          </span>
          <span className="text-xs px-2 py-0.5 rounded-xs bg-slate-100 text-text-secondary border border-border-subtle font-mono">
            {isPlatformHost ? 'Platform Host' : 'Factory Client'}
          </span>
        </div>

        <div className="flex items-center gap-4 text-xs">
          <div className="flex items-center gap-2 text-text-secondary">
            <UserIcon className="w-4 h-4 text-text-muted" />
            <span className="font-medium">{user?.name}</span>
            <span className="text-text-muted">({user?.username})</span>
          </div>

          <Button
            variant="outline"
            size="sm"
            onClick={handleLogout}
            leftIcon={<LogOut className="w-3.5 h-3.5" />}
          >
            Sign Out
          </Button>
        </div>
      </header>

      {/* Main Container */}
      <main className="flex-1 p-6 sm:p-8 max-w-7xl w-full mx-auto flex flex-col gap-6">
        {/* Welcome Banner */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-border-subtle">
          <div>
            <h1 className="text-xl font-bold tracking-tight text-text-primary">
              {isPlatformHost ? 'Platform Command Center' : 'Apparel Operations Hub'}
            </h1>
            <p className="text-xs text-text-muted mt-0.5">
              Active Session Route: <code className="text-brand-accent font-mono">{dashboardTarget}</code>
            </p>
          </div>

          <div className="flex items-center gap-2">
            <span className="text-xs text-text-secondary font-medium">Active Workspace:</span>
            <span className="text-xs px-2.5 py-1 rounded-sm bg-blue-50 text-brand-accent border border-blue-200 font-medium">
              {company?.name || 'TraceFlow Engine'}
            </span>
          </div>
        </div>

        {/* Dashboard Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <Card className="flex flex-col gap-3">
            <div className="flex items-center gap-2.5 text-brand-accent">
              <Shield className="w-5 h-5" />
              <h3 className="font-semibold text-sm text-text-primary">IAM Context</h3>
            </div>
            <div className="text-xs space-y-1.5 text-text-secondary">
              <p>
                <span className="text-text-muted">User ID:</span>{' '}
                <span className="font-mono text-[11px]">{user?.id}</span>
              </p>
              <p>
                <span className="text-text-muted">Platform Admin:</span>{' '}
                <span className="font-semibold text-emerald-600">
                  {user?.is_platform_admin ? 'Yes (Bypass Active)' : 'No'}
                </span>
              </p>
              <p>
                <span className="text-text-muted">Assigned Roles:</span>{' '}
                <span className="font-mono">{user?.roles.join(', ') || 'None'}</span>
              </p>
            </div>
          </Card>

          <Card className="flex flex-col gap-3">
            <div className="flex items-center gap-2.5 text-brand-primary">
              <Building2 className="w-5 h-5" />
              <h3 className="font-semibold text-sm text-text-primary">Tenant Entity</h3>
            </div>
            <div className="text-xs space-y-1.5 text-text-secondary">
              <p>
                <span className="text-text-muted">Company Code:</span>{' '}
                <span className="font-mono font-bold">{company?.code || 'ROOT-PLATFORM'}</span>
              </p>
              <p>
                <span className="text-text-muted">Company Name:</span> {company?.name}
              </p>
              <p>
                <span className="text-text-muted">Host Entity:</span>{' '}
                {company?.is_platform_host ? 'Platform Owner Engine' : 'Factory Tenant'}
              </p>
            </div>
          </Card>

          <Card className="flex flex-col gap-3">
            <div className="flex items-center gap-2.5 text-slate-700">
              <LayoutDashboard className="w-5 h-5" />
              <h3 className="font-semibold text-sm text-text-primary">Dual Dashboard Status</h3>
            </div>
            <p className="text-xs text-text-secondary leading-relaxed">
              {isPlatformHost
                ? 'Superadmin root control plane with full system bypass and appliance management permissions.'
                : 'Apparel operational floor hub with sewing lines, cutting batches, and garment bundle trackers.'}
            </p>
          </Card>
        </div>
      </main>
    </div>
  );
};
