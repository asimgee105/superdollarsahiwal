import * as React from "react";
import { cn } from "@/lib/utils";

export interface LoaderProps extends React.HTMLAttributes<HTMLDivElement> {
  size?: "sm" | "md" | "lg";
}

export function Loader({ size = "md", className, ...props }: LoaderProps) {
  return (
    <div
      className={cn(
        "animate-spin rounded-full border-t-transparent border-current",
        {
          "h-4 w-4 border-2": size === "sm",
          "h-8 w-8 border-3": size === "md",
          "h-12 w-12 border-4": size === "lg",
        },
        "text-primary",
        className
      )}
      role="status"
      aria-label="loading"
      {...props}
    />
  );
}
