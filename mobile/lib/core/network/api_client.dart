import 'dart:math';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../storage/hive_storage.dart';

class ApiClient {
  final Dio _dio;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  // Primary URL matching backend domain, mapping to local fallbacks for emulation
  static const String baseUrl = 'http://api.superdollarsahiwal.com';

  ApiClient()
      : _dio = Dio(BaseOptions(
          baseUrl: baseUrl,
          connectTimeout: const Duration(seconds: 5),
          receiveTimeout: const Duration(seconds: 5),
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
        )) {
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        // Appends Sanctum Auth Token if available
        final token = await _storage.read(key: 'auth_token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }

        // Appends X-Session-Key for Guest Cart / Guest wishlist management
        String? sessionKey = HiveStorage.settings.get('session_key');
        if (sessionKey == null) {
          sessionKey = _generateSessionKey();
          await HiveStorage.settings.put('session_key', sessionKey);
        }
        options.headers['X-Session-Key'] = sessionKey;

        return handler.next(options);
      },
      onError: (DioException error, handler) async {
        // Intercept network failures to serve offline cache fallbacks
        if (error.type == DioExceptionType.connectionTimeout ||
            error.type == DioExceptionType.connectionError ||
            error.type == DioExceptionType.receiveTimeout) {
          
          final path = error.requestOptions.path;
          
          // Return cached mock data if endpoints fail
          if (path.contains('/api/v1/products')) {
            final cache = HiveStorage.catalog.get('products');
            if (cache != null) {
              return handler.resolve(Response(
                requestOptions: error.requestOptions,
                data: {'data': cache, 'success': true},
                statusCode: 200,
              ));
            }
          } else if (path.contains('/api/v1/categories')) {
            final cache = HiveStorage.catalog.get('categories');
            if (cache != null) {
              return handler.resolve(Response(
                requestOptions: error.requestOptions,
                data: cache,
                statusCode: 200,
              ));
            }
          } else if (path.contains('/api/v1/settings') || path.contains('/api/v1/theme')) {
            final cache = HiveStorage.settings.get('app_settings');
            if (cache != null) {
              return handler.resolve(Response(
                requestOptions: error.requestOptions,
                data: cache,
                statusCode: 200,
              ));
            }
          }
        }
        return handler.next(error);
      },
    ));
  }

  static String _generateSessionKey() {
    final random = Random();
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    return List.generate(32, (index) => chars[random.nextInt(chars.length)]).join();
  }

  Future<Response> get(String path, {Map<String, dynamic>? queryParameters}) async {
    try {
      final res = await _dio.get(path, queryParameters: queryParameters);
      _cacheDataIfNeeded(path, res.data);
      return res;
    } on DioException catch (e) {
      if (e.response != null) return e.response!;
      rethrow;
    }
  }

  Future<Response> post(String path, {dynamic data}) async {
    try {
      return await _dio.post(path, data: data);
    } on DioException catch (e) {
      if (e.response != null) return e.response!;
      rethrow;
    }
  }

  Future<Response> put(String path, {dynamic data}) async {
    try {
      return await _dio.put(path, data: data);
    } on DioException catch (e) {
      if (e.response != null) return e.response!;
      rethrow;
    }
  }

  Future<Response> delete(String path, {dynamic data}) async {
    try {
      return await _dio.delete(path, data: data);
    } on DioException catch (e) {
      if (e.response != null) return e.response!;
      rethrow;
    }
  }

  void _cacheDataIfNeeded(String path, dynamic data) {
    if (data == null) return;
    if (path.contains('/api/v1/products') && data is Map && data['data'] != null) {
      HiveStorage.catalog.put('products', data['data']);
    } else if (path.contains('/api/v1/categories')) {
      HiveStorage.catalog.put('categories', data);
    } else if ((path.contains('/api/v1/settings') || path.contains('/api/v1/theme')) && data is Map) {
      HiveStorage.settings.put('app_settings', data);
    }
  }
}
