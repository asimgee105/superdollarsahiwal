"use client";

import * as React from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { useAuthStore } from "@/store/slices/authSlice";
import { authApi } from "@/features/auth/api";
import { ApiError } from "@/lib/api";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Loader } from "@/components/ui/loader";

export default function RegisterPage() {
  const router = useRouter();
  const { setCredentials, isAuthenticated, initializeAuth } = useAuthStore();

  const [name, setName] = React.useState("");
  const [email, setEmail] = React.useState("");
  const [password, setPassword] = React.useState("");
  const [passwordConfirmation, setPasswordConfirmation] = React.useState("");
  
  const [isLoading, setIsLoading] = React.useState(false);
  const [fieldErrors, setFieldErrors] = React.useState<Record<string, string[]>>({});
  const [authProviders, setAuthProviders] = React.useState({
    google: false,
    facebook: false,
    apple: false,
    github: false,
    microsoft: false,
    linkedin: false,
    twitter: false,
    phone_login: true,
  });

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    initializeAuth();
    if (isAuthenticated) {
      router.push("/");
    }
  }, [isAuthenticated, initializeAuth, router]);

  React.useEffect(() => {
    fetch(`${API_URL}/api/v1/settings?nocache=1`)
      .then((res) => res.json())
      .then((data) => {
        if (data.auth_providers) {
          setAuthProviders(data.auth_providers);
        }
      })
      .catch(() => {});
  }, [API_URL]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setFieldErrors({});

    if (password !== passwordConfirmation) {
      setFieldErrors({
        password_confirmation: ["Passwords do not match."],
      });
      setIsLoading(false);
      return;
    }

    try {
      const response = await authApi.register({
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
      });
      setCredentials(response.data.user, response.data.token);
      toast.success("Account created successfully!");
      router.push("/");
    } catch (err) {
      if (err instanceof ApiError) {
        if (err.errors) {
          setFieldErrors(err.errors);
        } else {
          toast.error(err.message);
        }
      } else {
        toast.error("Failed to connect to authentication server.");
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex min-h-[75vh] items-center justify-center px-4 py-12">
      <Card className="w-full max-w-md border border-border shadow-md">
        <CardHeader className="space-y-1 text-center">
          <CardTitle className="font-heading text-2xl font-bold uppercase tracking-wider text-foreground">
            Register
          </CardTitle>
          <CardDescription className="text-xs text-muted-foreground">
            Create an account to start curating your personal wardrobe
          </CardDescription>
        </CardHeader>
        <form onSubmit={handleSubmit}>
          <CardContent className="space-y-4">
            {/* Name Field */}
            <div className="space-y-1.5">
              <Label htmlFor="name" className="text-xs font-semibold uppercase tracking-wider">
                Full Name
              </Label>
              <Input
                id="name"
                type="text"
                placeholder="Jane Doe"
                value={name}
                onChange={(e) => setName(e.target.value)}
                className="text-xs bg-muted/40 border-border"
                required
              />
              {fieldErrors.name && (
                <p className="text-[10px] font-medium text-destructive">{fieldErrors.name[0]}</p>
              )}
            </div>

            {/* Email Field */}
            <div className="space-y-1.5">
              <Label htmlFor="email" className="text-xs font-semibold uppercase tracking-wider">
                Email Address
              </Label>
              <Input
                id="email"
                type="email"
                placeholder="name@domain.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="text-xs bg-muted/40 border-border"
                required
              />
              {fieldErrors.email && (
                <p className="text-[10px] font-medium text-destructive">{fieldErrors.email[0]}</p>
              )}
            </div>

            {/* Password Field */}
            <div className="space-y-1.5">
              <Label htmlFor="password" className="text-xs font-semibold uppercase tracking-wider">
                Password
              </Label>
              <Input
                id="password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="text-xs bg-muted/40 border-border"
                required
              />
              {fieldErrors.password && (
                <p className="text-[10px] font-medium text-destructive">{fieldErrors.password[0]}</p>
              )}
            </div>

            {/* Confirm Password Field */}
            <div className="space-y-1.5">
              <Label htmlFor="password_confirmation" className="text-xs font-semibold uppercase tracking-wider">
                Confirm Password
              </Label>
              <Input
                id="password_confirmation"
                type="password"
                value={passwordConfirmation}
                onChange={(e) => setPasswordConfirmation(e.target.value)}
                className="text-xs bg-muted/40 border-border"
                required
              />
              {fieldErrors.password_confirmation && (
                <p className="text-[10px] font-medium text-destructive">
                  {fieldErrors.password_confirmation[0]}
                </p>
              )}
            </div>
          </CardContent>
          <CardFooter className="flex flex-col gap-4 mt-2">
            <Button type="submit" className="w-full text-xs uppercase tracking-wider font-bold py-5" disabled={isLoading}>
              {isLoading ? <Loader size="sm" className="text-primary-foreground" /> : "Register"}
            </Button>

            {/* Dynamic Social Login Options */}
            {(authProviders.google || authProviders.facebook || authProviders.apple || authProviders.github || authProviders.microsoft || authProviders.linkedin || authProviders.twitter) && (
              <div className="w-full border-t border-border pt-4 flex flex-col gap-2">
                <div className="text-center text-[10px] text-muted-foreground font-bold uppercase tracking-wider mb-1">
                  Or Sign Up With
                </div>
                <div className="grid grid-cols-2 gap-2 w-full">
                  {authProviders.google && (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => window.location.href = `${API_URL}/auth/google/redirect`}
                      className="text-[10px] font-bold uppercase tracking-wider py-2 h-auto"
                    >
                      Google
                    </Button>
                  )}
                  {authProviders.facebook && (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => window.location.href = `${API_URL}/auth/facebook/redirect`}
                      className="text-[10px] font-bold uppercase tracking-wider py-2 h-auto"
                    >
                      Facebook
                    </Button>
                  )}
                  {authProviders.apple && (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => window.location.href = `${API_URL}/auth/apple/redirect`}
                      className="text-[10px] font-bold uppercase tracking-wider py-2 h-auto"
                    >
                      Apple
                    </Button>
                  )}
                  {authProviders.github && (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => window.location.href = `${API_URL}/auth/github/redirect`}
                      className="text-[10px] font-bold uppercase tracking-wider py-2 h-auto"
                    >
                      GitHub
                    </Button>
                  )}
                  {authProviders.microsoft && (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => window.location.href = `${API_URL}/auth/microsoft/redirect`}
                      className="text-[10px] font-bold uppercase tracking-wider py-2 h-auto"
                    >
                      Microsoft
                    </Button>
                  )}
                  {authProviders.linkedin && (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => window.location.href = `${API_URL}/auth/linkedin/redirect`}
                      className="text-[10px] font-bold uppercase tracking-wider py-2 h-auto"
                    >
                      LinkedIn
                    </Button>
                  )}
                  {authProviders.twitter && (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => window.location.href = `${API_URL}/auth/twitter/redirect`}
                      className="text-[10px] font-bold uppercase tracking-wider py-2 h-auto col-span-2"
                    >
                      X (Twitter)
                    </Button>
                  )}
                </div>
              </div>
            )}

            <p className="text-center text-[10px] text-muted-foreground">
              Already have an account?{" "}
              <Link href="/login" className="font-semibold text-foreground hover:underline">
                Sign In
              </Link>
            </p>
          </CardFooter>
        </form>
      </Card>
    </div>
  );
}
