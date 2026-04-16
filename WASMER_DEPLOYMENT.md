# Wasmer Deployment Guide: EDU-ID SaaS

This document provides a step-by-step guide to hosting your Laravel-based **EDU-ID SaaS** application on [Wasmer Edge](https://wasmer.io).

## 1. Prerequisites

Before you begin, ensure you have:
1.  **Wasmer Account**: Sign up at [wasmer.io](https://wasmer.io).
2.  **Wasmer CLI**: Install it using the command:
    ```powershell
    iwr https://wasmer.io/install.ps1 -useb | iex
    ```
    *Note: You may need to restart your terminal after installation.*

## 2. Prepare Your Application

### Database Setup
Wasmer Edge is serverless and does not provide an internal database. You **must** use an external managed database (e.g., PlanetScale, Supabase, DigitalOcean, or AWS RDS).

1.  Create a MySQL database on your preferred provider.
2.  Update your `wasmer.toml` or set environment variables during deployment with your database credentials:
    - `DB_HOST`: The endpoint of your external database.
    - `DB_PORT`: Usually `3306`.
    - `DB_DATABASE`: Your database name.
    - `DB_USERNAME`: Your database username.
    - `DB_PASSWORD`: Your database password.

### Environment Configuration
The `wasmer.toml` already contains some production settings. Ensure the `APP_URL` in `wasmer.toml` (line 33) matches your intended deployment URL (e.g., `https://edu-id-saas.wasmer.app`).

## 3. Deployment Steps

Follow these commands in your terminal from the project root:

### Step A: Login to Wasmer
```bash
wasmer login
```
This will open a browser window for authentication.

### Step B: Validate Configuration
You can test the package configuration locally using:
```bash
wasmer run .
```

### Step C: Deploy to Wasmer Edge
Run the following command to bundle your files and deploy:
```bash
wasmer deploy
```
- The CLI will detect the `wasmer.toml` file.
- It will upload the contents (minus anything in `.wasmerignore`).
- It will provide you with a deployment URL once finished.

## 4. Troubleshooting

- **Missing Files**: If vendor or public assets are missing, ensure they are correctly mapped in the `[fs]` section of `wasmer.toml`.
- **Database Connection**: Ensure your external database allows connections from any IP (`0.0.0.0/0`) or specifically from Wasmer Edge's egress IPs.
- **Environment Variables**: You can also manage secrets via the Wasmer Dashboard for more security instead of hardcoding them in `wasmer.toml`.

## 5. File Structure Reference
- `wasmer.toml`: Deployment and runner configuration.
- `.wasmerignore`: Specifies which files to exclude from the upload.

---
**Happy Deploying!** 🚀
