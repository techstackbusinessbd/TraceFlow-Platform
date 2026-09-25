import React from 'react';

export interface AlertProps {
  variant?: 'danger' | 'warning' | 'success' | 'info';
  title?: string;
  children: React.ReactNode;
  icon?: React.ReactNode;
  className?: string;
}

/**
 * Enterprise Alert Component (ADR-16 & Tailwind CSS v4)
 *
 * Muted, anti-fatigue color signals based on centralized Tailwind tokens.
 */
export const Alert: React.FC<AlertProps> = ({
  variant = 'info',
  title,
  children,
  icon,
  className = '',
}) => {
  const variantClasses = {
    danger: 'bg-signal-danger-bg text-signal-danger-text border-signal-danger-border',
    warning: 'bg-signal-warning-bg text-signal-warning-text border-signal-warning-border',
    success: 'bg-signal-success-bg text-signal-success-text border-signal-success-border',
    info: 'bg-signal-info-bg text-signal-info-text border-signal-info-border',
  }[variant];

  return (
    <div className={`flex items-start gap-2.5 px-3.5 py-2.5 rounded-sm border text-xs leading-relaxed ${variantClasses} ${className}`} role="alert">
      {icon && <span className="flex items-center shrink-0 mt-0.5">{icon}</span>}
      <div className="flex flex-col gap-0.5">
        {title && <h5 className="font-semibold text-xs">{title}</h5>}
        <div className="font-normal">{children}</div>
      </div>
    </div>
  );
};
