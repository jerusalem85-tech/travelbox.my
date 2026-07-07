const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

async function request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
  const token = typeof window !== "undefined" ? localStorage.getItem("travelbox_token") : null;
  const headers: Record<string, string> = {
    "Content-Type": "application/json",
    ...(options.headers as Record<string, string>),
  };
  if (token) headers["Authorization"] = `Bearer ${token}`;

  const res = await fetch(`${API_BASE}${endpoint}`, { ...options, headers });
  if (!res.ok) {
    const error = await res.json().catch(() => ({ message: res.statusText }));
    throw new Error(error.message || `Request failed: ${res.status}`);
  }
  return res.json();
}

export const api = {
  get: <T>(endpoint: string) => request<T>(endpoint),
  post: <T>(endpoint: string, data: unknown) => request<T>(endpoint, { method: "POST", body: JSON.stringify(data) }),
  patch: <T>(endpoint: string, data: unknown) => request<T>(endpoint, { method: "PATCH", body: JSON.stringify(data) }),
  delete: <T>(endpoint: string) => request<T>(endpoint, { method: "DELETE" }),

  auth: {
    login: (email: string, password: string) =>
      request<{ accessToken: string; user: { id: string; email: string; firstName: string; lastName: string; role: string } }>("/auth/login", {
        method: "POST",
        body: JSON.stringify({ email, password }),
      }),
    profile: () => request<{ id: string; email: string; firstName: string; lastName: string; role: string }>("/auth/profile"),
  },
};
