import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../../core/network/api_client.dart';

// States
abstract class AuthState {}
class AuthInitial extends AuthState {}
class AuthLoading extends AuthState {}
class OtpSentState extends AuthState {
  final int resendDelay;
  OtpSentState(this.resendDelay);
}
class NewUserRegistrationRequired extends AuthState {
  final String email;
  NewUserRegistrationRequired(this.email);
}
class AuthSuccess extends AuthState {
  final Map<String, dynamic> user;
  AuthSuccess(this.user);
}
class AuthFailure extends AuthState {
  final String error;
  AuthFailure(this.error);
}

// Events
abstract class AuthEvent {}
class SendOtpEvent extends AuthEvent {
  final String email;
  SendOtpEvent(this.email);
}
class VerifyOtpEvent extends AuthEvent {
  final String email;
  final String otp;
  VerifyOtpEvent(this.email, this.otp);
}
class CompleteProfileEvent extends AuthEvent {
  final String email;
  final String name;
  final String phone;
  final String password;
  final String passwordConfirmation;
  CompleteProfileEvent({
    required this.email,
    required this.name,
    required this.phone,
    required this.password,
    required this.passwordConfirmation,
  });
}
class PasswordLoginEvent extends AuthEvent {
  final String email;
  final String password;
  PasswordLoginEvent(this.email, this.password);
}

// Bloc implementation
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final ApiClient _apiClient = ApiClient();
  final _storage = const FlutterSecureStorage();

  AuthBloc() : super(AuthInitial()) {
    on<SendOtpEvent>((event, emit) async {
      emit(AuthLoading());
      try {
        final response = await _apiClient.post('/api/v1/auth/otp/send', data: {
          'email': event.email,
        });
        if (response.statusCode == 200) {
          final resData = response.data['data'] ?? {};
          final delay = resData['resend_delay'] ?? 60;
          emit(OtpSentState(delay));
        } else {
          emit(AuthFailure(response.data['message'] ?? 'Failed to send OTP code.'));
        }
      } catch (e) {
        emit(AuthFailure(_handleDioError(e)));
      }
    });

    on<VerifyOtpEvent>((event, emit) async {
      emit(AuthLoading());
      try {
        final response = await _apiClient.post('/api/v1/auth/otp/verify', data: {
          'email': event.email,
          'otp': event.otp,
        });
        final isSuccess = response.data['success'] ?? false;
        if (isSuccess) {
          final resData = response.data['data'] ?? {};
          final isNewUser = resData['is_new_user'] ?? false;
          if (isNewUser) {
            emit(NewUserRegistrationRequired(event.email));
          } else {
            final token = resData['token'];
            final user = resData['user'];
            await _storage.write(key: 'auth_token', value: token);
            emit(AuthSuccess(user));
          }
        } else {
          emit(AuthFailure(response.data['message'] ?? 'Invalid OTP code.'));
        }
      } catch (e) {
        emit(AuthFailure(_handleDioError(e)));
      }
    });

    on<CompleteProfileEvent>((event, emit) async {
      emit(AuthLoading());
      try {
        final response = await _apiClient.post('/api/v1/auth/register-complete', data: {
          'email': event.email,
          'name': event.name,
          'phone': event.phone,
          'password': event.password,
          'password_confirmation': event.passwordConfirmation,
        });
        final isSuccess = response.data['success'] ?? false;
        if (isSuccess) {
          final resData = response.data['data'] ?? {};
          final token = resData['token'];
          final user = resData['user'];
          await _storage.write(key: 'auth_token', value: token);
          emit(AuthSuccess(user));
        } else {
          emit(AuthFailure(response.data['message'] ?? 'Profile completion failed.'));
        }
      } catch (e) {
        emit(AuthFailure(_handleDioError(e)));
      }
    });

    on<PasswordLoginEvent>((event, emit) async {
      emit(AuthLoading());
      try {
        final response = await _apiClient.post('/api/v1/auth/login', data: {
          'email': event.email,
          'password': event.password,
        });
        final isSuccess = response.data['success'] ?? false;
        if (isSuccess) {
          final resData = response.data['data'] ?? {};
          final token = resData['token'];
          final user = resData['user'];
          await _storage.write(key: 'auth_token', value: token);
          emit(AuthSuccess(user));
        } else {
          emit(AuthFailure(response.data['message'] ?? 'Incorrect credentials.'));
        }
      } catch (e) {
        emit(AuthFailure(_handleDioError(e)));
      }
    });
  }

  String _handleDioError(dynamic e) {
    if (e is DioException) {
      return e.response?.data['message'] ?? e.message ?? 'Network connection failed.';
    }
    return e.toString();
  }
}
