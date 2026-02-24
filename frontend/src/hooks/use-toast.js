"use client";

import * as React from "react";

const ToastContext = React.createContext(null);

export const ToastProvider = ({ children }) => {
  const [toasts, setToasts] = React.useState([]);

  const toast = ({ title, description, variant = "default" }) => {
    setToasts((prev) => [
      ...prev,
      { id: Date.now(), title, description, variant },
    ]);

    // auto remove after 3 sec
    setTimeout(() => {
      setToasts((prev) => prev.slice(1));
    }, 3000);
  };

  return (
    <ToastContext.Provider value={{ toast }}>
      {children}

      {/* Toast UI */}
      <div className="fixed top-4 right-4 z-50 space-y-2">
        {toasts.map((t) => (
          <div
            key={t.id}
            className={`rounded-lg px-4 py-3 shadow-lg text-white
              ${
                t.variant === "destructive"
                  ? "bg-red-500"
                  : "bg-gray-900"
              }`}
          >
            <p className="font-semibold">{t.title}</p>
            {t.description && (
              <p className="text-sm opacity-90">{t.description}</p>
            )}
          </div>
        ))}
      </div>
    </ToastContext.Provider>
  );
};

export const useToast = () => {
  const ctx = React.useContext(ToastContext);
  if (!ctx) {
    throw new Error("useToast must be used inside ToastProvider");
  }
  return ctx;
};
