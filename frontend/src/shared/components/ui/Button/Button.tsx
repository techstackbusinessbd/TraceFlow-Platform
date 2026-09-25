import React from 'react';
import './Button.css';

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'outline' | 'danger' | 'ghost';
  size?: 'sm' | 'default' | 'lg';
  isLoading?: boolean;
  leftIcon?: React.ReactNode;
  rightIcon?: React.ReactNode;
}

/**
 * Enterprise Button Component (ADR-16 & UI-UX-GUIDELINES)
 *
 * Fully centralized styling backed by design tokens.
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
  return (
    <button
      className={`tf-button tf-button--${variant} tf-button--${size} ${className}`}
      disabled={disabled || isLoading}
      {...props}
    >
      {isLoading ? (
        <span className="tf-button__spinner" aria-hidden="true" />
      ) : (
        leftIcon && <span className="tf-button__icon tf-button__icon--left">{leftIcon}</span>
      )}
      <span className="tf-button__content">{children}</span>
      {!isLoading && rightIcon && (
        <span className="tf-button__icon tf-button__icon--right">{rightIcon}</span>
      )}
    </button>
  );
};
