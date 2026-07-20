import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';

class AIShoppingAssistant extends StatefulWidget {
  const AIShoppingAssistant({super.key});

  @override
  State<AIShoppingAssistant> createState() => _AIShoppingAssistantState();
}

class _AIShoppingAssistantState extends State<AIShoppingAssistant> {
  final _controller = TextEditingController();
  final List<Map<String, dynamic>> _messages = [
    {
      'sender': 'ai',
      'text': 'Hi! I am your Super Dollar AI Shopping Assistant. Ask me anything about sizing advice, matching styles, or catalog recommendations!',
      'time': '10:00 AM'
    }
  ];
  bool _thinking = false;

  void _sendQuery() {
    final text = _controller.text.trim();
    if (text.isEmpty) return;

    _controller.clear();
    setState(() {
      _messages.add({
        'sender': 'user',
        'text': text,
        'time': '10:01 AM',
      });
      _thinking = true;
    });

    // Simulate AI response logic
    Future.delayed(const Duration(seconds: 1.5), () {
      if (mounted) {
        String aiResponse = 'I can help you matching styles. For cotton kurta sets, I suggest pairing them with white leather sandals and ethnic pajama pants.';
        
        final lower = text.toLowerCase();
        if (lower.contains('size') || lower.contains('sizing')) {
          aiResponse = '• Size Guide: Our casual denims fit true to size. If you are in between sizes, order one size up for comfort.';
        } else if (lower.contains('return') || lower.contains('policy')) {
          aiResponse = '• Returns Info: You can file returns directly inside the app within 7 days of package delivery. Shipping is free for returns.';
        } else if (lower.contains('coupon') || lower.contains('discount')) {
          aiResponse = '• Store Vouchers: Try using coupon "SD20" at checkout to redeem a flat 20% discount on denims!';
        }

        setState(() {
          _thinking = false;
          _messages.add({
            'sender': 'ai',
            'text': aiResponse,
            'time': '10:02 AM',
          });
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.auto_awesome, color: AppTheme.primaryColor, size: 18),
            SizedBox(width: 8),
            Text('AI SHOPPING ASSISTANT', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
          ],
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/home'),
        ),
      ),
      body: Column(
        children: [
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: _messages.length,
              itemBuilder: (context, index) {
                final msg = _messages[index];
                final isUser = msg['sender'] == 'user';

                return Align(
                  alignment: isUser ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: isUser ? AppTheme.primaryColor : Colors.white,
                      border: isUser ? null : Border.all(color: Colors.grey.shade100),
                      borderRadius: BorderRadius.only(
                        topLeft: const Radius.circular(12),
                        topRight: const Radius.circular(12),
                        bottomLeft: isUser ? const Radius.circular(12) : Radius.zero,
                        bottomRight: isUser ? Radius.zero : const Radius.circular(12),
                      ),
                      boxShadow: isUser
                          ? null
                          : [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.01),
                                blurRadius: 10,
                                offset: const Offset(0, 4),
                              )
                            ],
                    ),
                    child: Column(
                      crossAxisAlignment: isUser ? CrossAxisAlignment.end : CrossAxisAlignment.start,
                      children: [
                        Text(
                          msg['text'],
                          style: TextStyle(color: isUser ? Colors.white : Colors.black87, fontSize: 11, fontWeight: FontWeight.w600, height: 1.4),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          msg['time'],
                          style: TextStyle(color: isUser ? Colors.white60 : Colors.grey, fontSize: 8),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),

          if (_thinking)
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
              child: Row(
                children: [
                  Icon(Icons.auto_awesome, size: 14, color: AppTheme.primaryColor),
                  SizedBox(width: 8),
                  Text('AI is thinking...', style: TextStyle(color: Colors.grey, fontSize: 8, fontWeight: FontWeight.bold)),
                ],
              ),
            ),

          // Message input bar
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white,
              border: Border(top: BorderSide(color: Colors.grey.shade200)),
            ),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _controller,
                    decoration: InputDecoration(
                      hintText: 'Ask AI about sizes, styles or vouchers...',
                      hintStyle: const TextStyle(fontSize: 12),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(20), borderSide: BorderSide.none),
                      filled: true,
                      fillColor: const Color(0xFFFAFAFA),
                    ),
                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                  ),
                ),
                const SizedBox(width: 8),
                IconButton(
                  icon: const Icon(Icons.send, color: AppTheme.primaryColor),
                  onPressed: _sendQuery,
                ),
              ],
            ),
          )
        ],
      ),
    );
  }
}
