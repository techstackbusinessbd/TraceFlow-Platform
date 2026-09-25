import React from 'react';

export interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  helperText?: string;
  leftIcon?: React.ReactNode;
  rightIcon?: React.ReactNode;
}

/**
 * Enterprise Form Input Component (ADR-16 & Tailwind CSS v4)
 *
 * Managed completely via centralized Tailwind theme utility classes with zero arbitrary inline CSS.
 */
export const Input = React.forwardRef<HTMLInputElement, InputProps>(({
  label,
  error,
  helperText,
  leftIcon,
  rightIcon,
  id,
  className = '',
  disabled,
  ...props
}, ref) => {
  const inputId = id || (label ? `input-${label.toLowerCase().replace(/\s+/g, '-')}` : undefined);

  return (
    <div className={`flex flex-col gap-1 w-full ${disabled ? 'opacity-60' : ''}`}>
      {label && (
        <label htmlFor={inputId} className="text-xs font-medium text-text-secondary select-none">
          {label}
        </label>
      )}
      <div className="relative flex items-center w-full">
        {leftIcon && (
          <span className="absolute left-3 flex items-center text-text-muted pointer-events-none">
            {leftIcon}
          </span>
        )}
        <input
          ref={ref}
          id={inputId}
          disabled={disabled}
          className={`w-full h-10 px-3.5 bg-bg-surface border rounded-sm text-sm text-text-primary placeholder:text-text-muted transition-all duration-150 outline-none
            ${leftIcon ? 'pl-9' : ''}
            ${rightIcon ? 'pr-9' : ''}
            ${error
              ? 'border-signal-danger-text focus:ring-2 focus:ring-red-500/20'
              : 'border-border-default focus:border-brand-accent focus:ring-2 focus:ring-brand-accent/20'
            }
            ${disabled ? 'bg-bg-surface-subtle cursor-not-allowed' : ''}
            ${className}
          `}
          {...props}
        />
        {rightIcon && (
          <span className="absolute right-3 flex items-center text-text-muted">
            {rightIcon}
          </span>
        )}
      </div>
      {error && <span className="text-xs font-medium text-signal-danger-text">{error}</span>}
      {!error && helperText && <span className="text-xs text-text-muted">{helperText}</span>}
    </div>
  );
});

Input.displayName = 'Input';
