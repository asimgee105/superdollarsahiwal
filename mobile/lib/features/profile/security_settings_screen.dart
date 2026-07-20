import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';

class SecuritySettingsScreen extends StatefulWidget {
  const SecuritySettingsScreen({super.key});

  @override
  State<SecuritySettingsScreen> createState() => _SecuritySettingsScreenState();
}

class _SecuritySettingsScreenState extends State<SecuritySettingsScreen> {
  bool _biometricsEnabled = true;
  bool _twoFactorEnabled = false;

  final List<Map<String, String>> _sessions = [
    {
      'device': 'Samsung Galaxy S24 (Mobile App)',
      'location': 'Lahore, Pakistan',
      'status': 'Active Session',
    },
    {
      'device': 'MacBook Pro (Chrome Browser)',
      'location': 'Lahore, Pakistan',
      'status': 'Logged in 2 hours ago',
    }
  ];

  void _revokeSession(int index) {
    setState(() {
      _sessions.removeAt(index);
    });
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Device session revoked successfully.')),
    );
  }

  void _confirmDeleteAccount() {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Text('DELETE ACCOUNT?', style: TextStyle(color: Colors.red, fontWeight: FontWeight.w900, fontSize: 14)),
          content: const Text(
            'WARNING: Deleting your shopper account is irreversible. All accumulated loyalty points, wallet credits, and order logs will be permanently deleted.',
            style: TextStyle(fontSize: 11, height: 1.5, color: Colors.black54),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('CANCEL', style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold, fontSize: 10)),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context); // Pop modal
                context.go('/login'); // Return to login
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Your account has been deleted.')),
                );
              },
              style: ElevatedButton.styleFrom(backgroundColor: Colors.red, foregroundColor: Colors.white),
              child: const Text('DELETE PERMANENTLY', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
            )
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('SECURITY SETTINGS'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/profile'),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Settings controls
            const Text('DEVICE PREFERENCES', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
            const SizedBox(height: 12),
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  SwitchListTile(
                    value: _biometricsEnabled,
                    title: const Text('Biometric Authentication', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                    subtitle: const Text('Unlock app using fingerprint or Face ID.', style: TextStyle(fontSize: 9)),
                    activeColor: AppTheme.primaryColor,
                    onChanged: (val) => setState(() => _biometricsEnabled = val),
                  ),
                  const Divider(height: 1),
                  SwitchListTile(
                    value: _twoFactorEnabled,
                    title: const Text('Two-Factor Authentication (2FA)', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                    subtitle: const Text('Request OTP code validation on every login attempt.', style: TextStyle(fontSize: 9)),
                    activeColor: AppTheme.primaryColor,
                    onChanged: (val) => setState(() => _twoFactorEnabled = val),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 28),

            // Active sessions
            const Text('ACTIVE SESSIONS', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
            const SizedBox(height: 12),
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: _sessions.length,
              itemBuilder: (context, index) {
                final session = _sessions[index];
                return Container(
                  margin: const EdgeInsets.only(bottom: 12),
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    border: Border.all(color: Colors.grey.shade200),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.important_devices, color: Colors.grey, size: 24),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              session['device']!,
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              '${session['location']} • ${session['status']}',
                              style: const TextStyle(fontSize: 9, color: Colors.grey),
                            ),
                          ],
                        ),
                      ),
                      if (index > 0)
                        IconButton(
                          icon: const Icon(Icons.logout, size: 16, color: Colors.red),
                          onPressed: () => _revokeSession(index),
                        ),
                    ],
                  ),
                );
              },
            ),
            const SizedBox(height: 28),

            // Danger zone
            const Text('DANGER ZONE', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
            const SizedBox(height: 12),
            ElevatedButton(
              onPressed: _confirmDeleteAccount,
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red.shade50,
                foregroundColor: Colors.red,
                elevation: 0,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('DELETE ACCOUNT PERMANENTLY', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
            )
          ],
        ),
      ),
    );
  }
}
