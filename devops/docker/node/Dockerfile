FROM node:18-alpine

WORKDIR /app

# Create necessary directories
RUN mkdir -p /app/frontend

# Copy dependency files
COPY frontend/package.json ./

# Install dependencies
RUN npm install

# Copy source code
COPY frontend/ .

# Expose port
EXPOSE 3000

# Start app in development mode
CMD [ "npm", "run", "dev" ] 