// File: resources/js/config/api.config.ts
interface ApiConfig {
    readonly BASE_URL: string;
    readonly TIMEOUT: number;
    readonly VERSION: string;
    readonly ENV: 'development' | 'production' | 'test';
}

export const API_CONFIG: ApiConfig = {
    BASE_URL: import.meta.env.VITE_API_URL || '/api',
    TIMEOUT: 30000,
    VERSION: 'v1',
    ENV: (import.meta.env.NODE_ENV as 'development' | 'production' | 'test') || 'development'
};

Object.freeze(API_CONFIG);