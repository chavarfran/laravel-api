FROM php:8.1

# ENV VARIABLES
ENV DEBIAN_FRONTEND="noninteractive"
ENV WORK_DIR="/opt/scripts"

# Setup user stuff
RUN echo 'root:root' | chpasswd
RUN mkdir -p "${WORK_DIR}"

# Install dependencies
RUN apt-get update && apt-get install -y \
      git curl zip unzip locales \
      # unicode font stuff
      libpq-dev libicu-dev libfreetype6-dev \
      # image stuff
      libjpeg-dev libpng-dev libjpeg62-turbo-dev\
      # compression
      libzip-dev zlib1g-dev \
      # xml
      libxml2-dev \
      # libonig: regex, libssldev (curl) libcurl4-openssl-dev
      libonig-dev libssl-dev

RUN sed -i 's/^# *\(en_US.UTF-8\)/\1/' /etc/locale.gen && locale-gen

# Configure php-ini
COPY php.ini "$PHP_INI_DIR/conf.d/99-marco-regulatorio.ini"

# Configure intl
RUN docker-php-ext-configure intl && \
    # Configure ftp
    docker-php-ext-configure ftp --with-openssl-dir=/usr && \
    # Configure gd
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    # Install base packages
    docker-php-ext-install -j$(nproc) \
      zip mysqli soap pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd xml intl ftp

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Install Node
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/include/node /usr/local/include/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
# Install yarn
COPY --from=node /opt/yarn-v*/bin/* /usr/local/bin/
COPY --from=node /opt/yarn-v*/lib/* /usr/local/lib/
# Link npm and yarn
RUN ln -vs /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -vs /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Do final cleanup
RUN apt-get -y autoremove && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Change the working directory
WORKDIR ${WORK_DIR}
EXPOSE 80 5173

# Copy base files
COPY --chmod=0755 entry-php.sh .
COPY --chmod=0755 entry-queue.sh .
COPY --chmod=0755 entry-node.sh .

WORKDIR /var/www/

# Use bash as entrypoint
ENTRYPOINT ["bash", "-c"]

# Prepare to startup the container
CMD ["/opt/scripts/entry-php.sh"]
