import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';
import 'bloc/auth_bloc.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _otpController = TextEditingController();
  final _authBloc = AuthBloc();

  bool _isOtpMode = true;
  bool _isOtpCodeSent = false;
  String _errorMessage = '';

  void _handleSubmit() {
    setState(() => _errorMessage = '');
    final email = _emailController.text.trim();
    if (email.isEmpty) {
      setState(() => _errorMessage = 'Please enter your email.');
      return;
    }

    if (_isOtpMode) {
      if (!_isOtpCodeSent) {
        _authBloc.add(SendOtpEvent(email));
      } else {
        final code = _otpController.text.trim();
        if (code.length < 4) {
          setState(() => _errorMessage = 'Please enter a valid OTP code.');
          return;
        }
        _authBloc.add(VerifyOtpEvent(email, code));
      }
    } else {
      final pass = _passwordController.text;
      if (pass.isEmpty) {
        setState(() => _errorMessage = 'Please enter your password.');
        return;
      }
      _authBloc.add(PasswordLoginEvent(email, pass));
    }
  }

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => _authBloc,
      child: Scaffold(
        backgroundColor: const Color(0xFFFAFAFA),
        appBar: AppBar(
          title: const Text('LOGIN / SIGNUP'),
        ),
        body: BlocConsumer<AuthBloc, AuthState>(
          listener: (context, state) {
            if (state is OtpSentState) {
              setState(() {
                _isOtpCodeSent = true;
              });
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('OTP sent to your email!')),
              );
            } else if (state is NewUserRegistrationRequired) {
              context.go('/complete-profile', extra: state.email);
            } else if (state is AuthSuccess) {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Login successful!')),
              );
              context.go('/home');
            } else if (state is AuthFailure) {
              setState(() => _errorMessage = state.error);
            }
          },
          builder: (context, state) {
            final loading = state is AuthLoading;

            return SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 30),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  
                  // Promo image card
                  Container(
                    height: 120,
                    decoration: BoxDecoration(
                      color: const Color(0xFFFFF3EB),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: Colors.amber.shade100),
                    ),
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Text(
                                'WELCOME TO SUPER DOLLAR',
                                style: TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w900,
                                  color: Color(0xFFFF3F6C),
                                ),
                              ),
                              SizedBox(height: 6),
                              Text(
                                'Sign in to access VIP insider rewards and order history.',
                                style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w600,
                                  color: Colors.grey,
                                  height: 1.4,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 12),
                        Icon(
                          Icons.shopping_bag_outlined,
                          size: 48,
                          color: const Color(0xFFFF3F6C).withOpacity(0.8),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 28),

                  // Mode tabs
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => setState(() {
                            _isOtpMode = true;
                            _isOtpCodeSent = false;
                            _errorMessage = '';
                          }),
                          style: OutlinedButton.styleFrom(
                            side: BorderSide(
                              color: _isOtpMode ? const Color(0xFFFF3F6C) : Colors.grey.shade300,
                              width: 1.5,
                            ),
                            backgroundColor: _isOtpMode ? const Color(0xFFFF3F6C).withOpacity(0.04) : Colors.transparent,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(8),
                            ),
                          ),
                          child: Text(
                            'OTP Sign-In',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w900,
                              color: _isOtpMode ? const Color(0xFFFF3F6C) : Colors.grey,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => setState(() {
                            _isOtpMode = false;
                            _errorMessage = '';
                          }),
                          style: OutlinedButton.styleFrom(
                            side: BorderSide(
                              color: !_isOtpMode ? const Color(0xFFFF3F6C) : Colors.grey.shade300,
                              width: 1.5,
                            ),
                            backgroundColor: !_isOtpMode ? const Color(0xFFFF3F6C).withOpacity(0.04) : Colors.transparent,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(8),
                            ),
                          ),
                          child: Text(
                            'Password Login',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w900,
                              color: !_isOtpMode ? const Color(0xFFFF3F6C) : Colors.grey,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  if (_errorMessage.isNotEmpty) ...[
                    Text(
                      _errorMessage,
                      style: const TextStyle(
                        color: Colors.red,
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),
                  ],

                  // Email field
                  const Text(
                    'EMAIL ADDRESS',
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w900,
                      color: Colors.black54,
                    ),
                  ),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _emailController,
                    keyboardType: TextInputType.emailAddress,
                    enabled: !loading && !_isOtpCodeSent,
                    decoration: InputDecoration(
                      hintText: 'Enter your email',
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: const BorderSide(color: Color(0xFFFF3F6C)),
                      ),
                    ),
                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 18),

                  // Password field (Password Mode)
                  if (!_isOtpMode) ...[
                    const Text(
                      'PASSWORD',
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: Colors.black54,
                      ),
                    ),
                    const SizedBox(height: 6),
                    TextField(
                      controller: _passwordController,
                      obscureText: true,
                      enabled: !loading,
                      decoration: InputDecoration(
                        hintText: 'Enter your password',
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide(color: Colors.grey.shade300),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: const BorderSide(color: Color(0xFFFF3F6C)),
                        ),
                      ),
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 18),
                  ],

                  // OTP verification field (OTP Mode + Code Sent)
                  if (_isOtpMode && _isOtpCodeSent) ...[
                    const Text(
                      'VERIFICATION CODE',
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: Colors.black54,
                      ),
                    ),
                    const SizedBox(height: 6),
                    TextField(
                      controller: _otpController,
                      keyboardType: TextInputType.number,
                      enabled: !loading,
                      maxLength: 6,
                      decoration: InputDecoration(
                        hintText: 'Enter 6-digit OTP code',
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                        counterText: '',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide(color: Colors.grey.shade300),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: const BorderSide(color: Color(0xFFFF3F6C)),
                        ),
                      ),
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, letterSpacing: 2.0),
                    ),
                    const SizedBox(height: 18),
                  ],

                  ElevatedButton(
                    onPressed: loading ? null : _handleSubmit,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFFF3F6C),
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.grey.shade400,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                      padding: const EdgeInsets.symmetric(vertical: 16),
                    ),
                    child: Text(
                      loading
                          ? 'PROCESSING...'
                          : (_isOtpMode
                              ? (_isOtpCodeSent ? 'VERIFY & CONTINUE' : 'SEND OTP CODE')
                              : 'LOGIN'),
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.2),
                    ),
                  ),

                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
