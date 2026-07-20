import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/network/api_client.dart';
import '../../core/theme/app_theme.dart';

class BlogsTestimonialsScreen extends StatefulWidget {
  const BlogsTestimonialsScreen({super.key});

  @override
  State<BlogsTestimonialsScreen> createState() => _BlogsTestimonialsScreenState();
}

class _BlogsTestimonialsScreenState extends State<BlogsTestimonialsScreen> {
  final ApiClient _apiClient = ApiClient();
  List<dynamic> _posts = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchPosts();
  }

  Future<void> _fetchPosts() async {
    try {
      final res = await _apiClient.get('/api/v1/posts');
      if (res.statusCode == 200 && res.data != null && res.data['data'] != null) {
        setState(() {
          _posts = res.data['data'];
          _isLoading = false;
        });
      } else {
        _loadFallbackPosts();
      }
    } catch (e) {
      _loadFallbackPosts();
    }
  }

  void _loadFallbackPosts() {
    setState(() {
      _posts = [
        {
          'title': 'The Ultimate Guide to Ethnic Cotton Weaves',
          'slug': 'ethnic-cotton-weaves',
          'image': 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=300&auto=format&fit=crop',
          'summary': 'Explore the history, craftsmanship, and styling details behind modern ethnic cotton weaves for summer wardrobes.',
          'published_date': 'July 18, 2026',
        },
        {
          'title': 'Sartorial Denim Secrets Revealed',
          'slug': 'sartorial-denim-secrets',
          'image': 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=300&auto=format&fit=crop',
          'summary': 'Find the optimal fit and wash matching techniques for slim-fit jeans designed to withstand high daily wear.',
          'published_date': 'July 14, 2026',
        }
      ];
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('FASHION DIARY & LOOKBOOKS'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/profile'),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primaryColor)))
          : ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: _posts.length,
              itemBuilder: (context, index) {
                final post = _posts[index];
                final title = post['title'] ?? '';
                final summary = post['summary'] ?? '';
                final image = post['image'] ?? 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=300&auto=format&fit=crop';
                final date = post['published_date'] ?? 'July 2026';

                return Card(
                  margin: const EdgeInsets.only(bottom: 20),
                  color: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  elevation: 0,
                  clipBehavior: Clip.antiAlias,
                  child: InkWell(
                    onTap: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text('Opening lookbook details for "$title"')),
                      );
                    },
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Image.network(image, height: 160, fit: BoxFit.cover),
                        Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(date.toUpperCase(), style: const TextStyle(fontSize: 8, color: AppTheme.primaryColor, fontWeight: FontWeight.w900)),
                              const SizedBox(height: 6),
                              Text(
                                title,
                                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.black87),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                summary,
                                style: const TextStyle(fontSize: 10, color: Colors.grey, height: 1.4),
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ],
                          ),
                        )
                      ],
                    ),
                  ),
                );
              },
            ),
    );
  }
}
