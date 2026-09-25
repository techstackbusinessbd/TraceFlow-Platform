import React from 'react';
import './Card.css';

export interface CardProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: 'default' | 'subtle' | 'outline';
  padding?: 'none' | 'sm' | 'md' | 'lg';
}

/**
 * Enterprise Card Component (ADR-16)
 *
 * Fully centralized styling backed by design tokens.
 */
export const Card: React.FC<CardProps> = ({
  children,
  variant = 'default',
  padding = 'md',
  className = '',
  ...props
}) => {
  return (
    <div className={`tf-card tf-card--${variant} tf-card--p-${padding} ${className}`} {...props}>
      {children}
    </div>
  );
};
