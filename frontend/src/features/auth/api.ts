import { api } from "@/lib/api";

export interface UserResponse {
  id: string;
  name: string;
  email: string;
  role: string;
}

export interface AuthResponse {
  success: boolean;
  data: {
    user: UserResponse;
    token: string;
  };
  message: string;
}

export interface ProfileResponse {
  success: boolean;
  data: {
    user: UserResponse;
  };
  message: string;
}

export const authApi = {
  login: (credentials: Record<string, string>) =>
    api.post<AuthResponse>("/auth/login", credentials),

  register: (details: Record<string, string>) =>
    api.post<AuthResponse>("/auth/register", details),

  logout: () =>
    api.post<{ success: boolean; message: string }>("/auth/logout"),

  getProfile: () =>
    api.get<ProfileResponse>("/auth/profile"),
};
