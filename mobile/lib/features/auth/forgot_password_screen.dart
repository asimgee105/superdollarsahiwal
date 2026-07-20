import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';

class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _emailController = TextEditingController();
  bool _loading = false;
  String _errorMessage = '';

  void _handleForgot() {
    final email = _emailController.text.trim();
    if (email.isEmpty) {
      setState(() => _errorMessage = 'Please enter your email.');
      return;
    }

    setState(() {
      _loading = true;
      _errorMessage = '';
    });

    // Simulate requesting OTP code
    Future.delayed(const Duration(seconds: 1), () {
      if (mounted) {
        setState(() => _loading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('OTP verification code sent successfully!')),
        );
        // Navigate to verify screen with email context
        context.go('/otp-verify', extra: email);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('FORGOT PASSWORD'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/login'),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 30),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text(
              'Forgot your password? Enter your email address below, and we will send you a 6-digit OTP code to verify your identity and reset your credentials.',
              style: TextStyle(fontSize: 11, color: Colors.grey, height: 1.5, fontWeight: FontWeight.w600),
            ),
            const SizedBox(height: 28),

            if (_errorMessage.isNotEmpty) ...[
              Text(
                _errorMessage,
                style: const TextStyle(color: Colors.red, fontSize: 11, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 12),
            ],

            const Text(
              'EMAIL ADDRESS',
              style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black54),
            ),
            const SizedBox(height: 6),
            TextField(
              controller: _emailController,
              keyboardType: TextInputType.emailAddress,
              enabled: !_loading,
              decoration: InputDecoration(
                hintText: 'Enter your account email',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                focusedBorder: const OutlineInputBorder(borderSide: BorderSide(color: AppTheme.primaryColor)),
              ),
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 28),

            ElevatedButton(
              onPressed: _loading ? null : _handleForgot,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primaryColor,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: Text(
                _loading ? 'REQUESTING CODE...' : 'SEND RESET CODE',
                style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.2),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
