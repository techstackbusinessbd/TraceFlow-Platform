import React from 'react';
import './Input.css';

export interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  helperText?: string;
  leftIcon?: React.ReactNode;
  rightIcon?: React.ReactNode;
}

/**
 * Enterprise Form Input Component (ADR-16 & SRS_LOGIN Section 2.3)
 *
 * Fully centralized styling backed by design tokens.
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
    <div className={`tf-input-group ${error ? 'tf-input-group--error' : ''} ${disabled ? 'tf-input-group--disabled' : ''}`}>
      {label && (
        <label htmlFor={inputId} className="tf-input-label">
          {label}
        </label>
      )}
      <div className="tf-input-wrapper">
        {leftIcon && <span className="tf-input-icon tf-input-icon--left">{leftIcon}</span>}
        <input
          ref={ref}
          id={inputId}
          disabled={disabled}
          className={`tf-input ${leftIcon ? 'tf-input--has-left-icon' : ''} ${rightIcon ? 'tf-input--has-right-icon' : ''} ${className}`}
          {...props}
        />
        {rightIcon && <span className="tf-input-icon tf-input-icon--right">{rightIcon}</span>}
      </div>
      {error && <span className="tf-input-error">{error}</span>}
      {!error && helperText && <span className="tf-input-helper">{helperText}</span>}
    </div>
  );
});

Input.displayName = 'Input';
