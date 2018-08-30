FROM ubuntu
# https://github.com/NatLibFi/Skosmos/wiki/Installation

RUN apt-get update -y && \
    apt-get install -y openssh-server && \
    apt-get install -y software-properties-common && \
    apt-add-repository -y ppa:openjdk-r/ppa && \
    apt-get update -y && \
    apt-get install -y openjdk-8-jre && \
    update-java-alternatives --set java-1.8.0-openjdk-amd64 && \
    cd && \
    wget https://archive.apache.org/dist/jena/binaries/apache-jena-fuseki-2.3.0.tar.gz && \
    cd /opt && \
    tar xzf ~/apache-jena-fuseki-2.3.0.tar.gz && \
    ln -s apache-jena-fuseki-2.3.0 fuseki && \
    cd /opt/fuseki/ && \
    adduser --system --home /opt/fuseki --no-create-home fuseki && \
    cd /var/lib && \
    mkdir -p fuseki/backups && \
    mkdir -p fuseki/databases && \
    mkdir -p fuseki/system && \
    mkdir -p fuseki/system_files && \
    chown -R fuseki fuseki && \
    cd /var/log && \
    mkdir fuseki && \
    chown fuseki fuseki && \
    cd /etc && \
    mkdir fuseki && \
    chown fuseki fuseki && \
    cd /etc/fuseki && \
    ln -s /var/lib/fuseki/* . && \
    ln -s /var/log/fuseki logs && \
    printf "export FUSEKI_HOME=/opt/fuseki\nexport FUSEKI_BASE=/etc/fuseki\n\nFUSEKI_USER=fuseki\nJAVA_OPTIONS=\"-Xmx2048M\"" > /etc/default/fuseki && \
    cd /etc/init.d && \
    ln -s /opt/fuseki/fuseki && \
    service fuseki start && \
    update-rc.d fuseki defaults && \
    service fuseki restart

ENV FUSEKI_HOME /opt/fuseki
ENV FUSEKI_BASE /etc/fuseki

COPY shiro.ini /etc/fuseki/shiro.ini
COPY docker-entrypoint.sh /
RUN chmod 755 /docker-entrypoint.sh

COPY load.sh /opt/fuseki/
COPY tdbloader /opt/fuseki/
RUN chmod 755 /opt/fuseki/load.sh /opt/fuseki/tdbloader

VOLUME /opt/fuseki/

WORKDIR /opt/fuseki/
EXPOSE 3030
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["/opt/fuseki/fuseki-server"]
