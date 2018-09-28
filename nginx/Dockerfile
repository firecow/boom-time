FROM node:8-alpine AS build-typescript
WORKDIR /app
COPY typescript/package.json /app/package.json
RUN npm install --progress=false --loglevel=error
COPY typescript /app
CMD ["npm", "start"]

FROM nginx:1.15.2
COPY --from=build-typescript /app/build/build.js /var/www/html/public/build/build.js
COPY nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY nginx/public /var/www/html/public
