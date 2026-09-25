import React from 'react';

export interface CardProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: 'default' | 'subtle' | 'outline';
  padding?: 'none' | 'sm' | 'md' | 'lg';
}

/**
 * Enterprise Card Component (ADR-16 & Tailwind CSS v4)
 */
export const Card: React.FC<CardProps> = ({
  children,
  variant = 'default',
  padding = 'md',
  className = '',
  ...props
}) => {
  const variantClasses = {
    default: 'bg-bg-surface border border-border-subtle shadow-card',
    subtle: 'bg-bg-surface-subtle border-transparent',
    outline: 'bg-transparent border border-border-default',
  }[variant];

  const paddingClasses = {
    none: 'p-0',
    sm: 'p-2',
    md: 'p-4',
    lg: 'p-6',
  }[padding];

  return (
    <div className={`rounded-md transition-all duration-150 ${variantClasses} ${paddingClasses} ${className}`} {...props}>
      {children}
    </div>
  );
};
