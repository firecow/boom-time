FROM node:8-alpine AS build-typescript
WORKDIR /app
COPY typescript/package.json /app/package.json
RUN npm install --progress=false --loglevel=error
COPY typescript /app
ENTRYPOINT npm start