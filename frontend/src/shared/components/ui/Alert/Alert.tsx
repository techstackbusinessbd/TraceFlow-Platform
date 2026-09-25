import React from 'react';
import './Alert.css';

export interface AlertProps {
  variant?: 'danger' | 'warning' | 'success' | 'info';
  title?: string;
  children: React.ReactNode;
  icon?: React.ReactNode;
  className?: string;
}

/**
 * Enterprise Alert Component (ADR-16 & SRS_LOGIN Section 2.4)
 *
 * Muted, anti-fatigue color signals based on design tokens.
 */
export const Alert: React.FC<AlertProps> = ({
  variant = 'info',
  title,
  children,
  icon,
  className = '',
}) => {
  return (
    <div className={`tf-alert tf-alert--${variant} ${className}`} role="alert">
      {icon && <span className="tf-alert__icon">{icon}</span>}
      <div className="tf-alert__body">
        {title && <h5 className="tf-alert__title">{title}</h5>}
        <div className="tf-alert__message">{children}</div>
      </div>
    </div>
  );
};
