FROM openjdk:8-alpine

ENV SONAR_SCANNER_VERSION 3.3.0.1492

RUN apk add --no-cache curl grep sed unzip bash nodejs nodejs-npm

# Set timezone to CST
ENV TZ=Europe/Minsk
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /var

RUN curl --insecure -o ./sonarscanner.zip -L https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-${SONAR_SCANNER_VERSION}-linux.zip && \
	unzip sonarscanner.zip && \
	rm sonarscanner.zip && \
	mv sonar-scanner-${SONAR_SCANNER_VERSION}-linux sonar-scanner

ENV SONAR_RUNNER_HOME=/var/sonar-scanner
ENV PATH $PATH:/var/sonar-scanner/bin

COPY ./data/sonar_scanner/sonar-scanner.properties ./sonar-scanner/conf/sonar-scanner.properties

#   ensure Sonar uses the provided Java for musl instead of a borked glibc one
RUN sed -i 's/use_embedded_jre=true/use_embedded_jre=false/g' /var/sonar-scanner/bin/sonar-scanner

CMD /bin/bash