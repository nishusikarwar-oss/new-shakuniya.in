


"use client";

import * as React from "react";
import { cn } from "@/lib/utils";

const Button = React.forwardRef(
  ({ className = "", type = "button", children, ...props }, ref) => {
    return (
      <button
        ref={ref}
        type={type}
        className={cn(
          "inline-flex items-center justify-center gap-2 rounded-lg px-6 py-3 text-sm font-medium transition-all duration-300",
          "bg-gradient-to-r from-purple-500 to-cyan-500 text-white",
          "hover:opacity-90 active:scale-95 disabled:opacity-50 disabled:pointer-events-none",
          className
        )}
        {...props}
      >
        {children}
      </button>
    );
  }
);

Button.displayName = "Button";
export { Button };
