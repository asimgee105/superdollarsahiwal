import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';

class ErrorDiagnosticScreen extends StatelessWidget {
  final String errorCode;
  final String? customMessage;
  const ErrorDiagnosticScreen({super.key, required this.errorCode, this.customMessage});

  @override
  Widget build(BuildContext context) {
    IconData icon = Icons.error_outline;
    String title = 'AN ERROR OCCURRED';
    String desc = customMessage ?? 'The server encountered an unexpected error processing your request. Please try again.';

    if (errorCode == '404') {
      icon = Icons.search_off_outlined;
      title = 'PAGE NOT FOUND';
      desc = 'The requested interface page could not be located in our routing registry.';
    } else if (errorCode == '500') {
      icon = Icons.dns_outlined;
      title = 'INTERNAL SERVER ERROR';
      desc = 'The back-end database servers returned a 500 error code. We are investigating it.';
    } else if (errorCode == 'permission') {
      icon = Icons.security_outlined;
      title = 'PERMISSION DENIED';
      desc = 'This interface requires location access or biometric authentication permissions to unlock.';
    }

    return Scaffold(
      backgroundColor: Colors.white,
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: const BoxDecoration(
                  color: Color(0xFFFAFAFA),
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, color: AppTheme.primaryColor, size: 64),
              ),
              const SizedBox(height: 24),
              Text(
                title,
                style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 13, letterSpacing: 1.0),
              ),
              const SizedBox(height: 8),
              Text(
                desc,
                style: const TextStyle(fontSize: 10, color: Colors.grey, height: 1.5),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 32),
              ElevatedButton(
                onPressed: () => context.go('/home'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryColor,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: const Text('BACK TO HOMEPAGE', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
              )
            ],
          ),
        ),
      ),
    );
  }
}
