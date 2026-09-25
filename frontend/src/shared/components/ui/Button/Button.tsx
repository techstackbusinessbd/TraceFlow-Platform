import React from 'react';

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'outline' | 'danger' | 'ghost';
  size?: 'sm' | 'default' | 'lg';
  isLoading?: boolean;
  leftIcon?: React.ReactNode;
  rightIcon?: React.ReactNode;
}

/**
 * Enterprise Button Component (ADR-16, UI-UX-GUIDELINES & Tailwind CSS v4)
 *
 * Managed completely via centralized Tailwind theme utility classes with zero arbitrary inline CSS.
 */
export const Button: React.FC<ButtonProps> = ({
  children,
  variant = 'primary',
  size = 'default',
  isLoading = false,
  leftIcon,
  rightIcon,
  disabled,
  className = '',
  ...props
}) => {
  const baseClasses =
    'inline-flex items-center justify-center font-medium rounded-sm transition-all duration-150 whitespace-nowrap select-none gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-accent/50 disabled:opacity-55 disabled:cursor-not-allowed';

  const sizeClasses = {
    sm: 'h-8 px-2.5 text-xs',
    default: 'h-10 px-4 text-sm',
    lg: 'h-12 px-6 text-base',
  }[size];

  const variantClasses = {
    primary: 'bg-brand-accent hover:bg-brand-accent-hover text-white shadow-sm',
    secondary: 'bg-brand-primary hover:bg-slate-800 text-white shadow-sm',
    outline: 'bg-transparent text-text-primary border border-border-default hover:bg-bg-surface-hover hover:border-border-strong',
    danger: 'bg-signal-danger-text hover:bg-red-800 text-white shadow-sm',
    ghost: 'bg-transparent text-text-secondary hover:bg-bg-surface-hover hover:text-text-primary',
  }[variant];

  return (
    <button
      className={`${baseClasses} ${sizeClasses} ${variantClasses} ${className}`}
      disabled={disabled || isLoading}
      {...props}
    >
      {isLoading ? (
        <span className="w-3.5 h-3.5 border-2 border-white/40 border-t-current rounded-full animate-spin" />
      ) : (
        leftIcon && <span className="flex items-center">{leftIcon}</span>
      )}
      <span>{children}</span>
      {!isLoading && rightIcon && <span className="flex items-center">{rightIcon}</span>}
    </button>
  );
};
