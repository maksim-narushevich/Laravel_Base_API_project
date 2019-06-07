**HOT TO RUN SONARQUBE SERVER FOR CODE INSPACTION**
--

* *Navigate to SonarQube service folder:*

        cd .services/docker/sonarqube
         
* *Create Docker network (if not created yet) for shared services:*
 
         docker network create docker-shared-services   
           
* *Start app and build required Docker containers:*

        docker-compose up -d
   
* *Sonarqube will be available on port `9010`:*

        http://127.0.0.1:9010
        
        login: admin
        password: admin   
      
* *Login into SonarQube and create new project with key `lara-api`:*

* *Obtain also **login key** ($LOGIN_TOKEN) after creation of new project:*

#### HOT TO BUILD & RUN SONARSCANNER CONTAINER
      
* *Navigate to app root folder:*

        cd /var/www
        
* *Before start building custom scanner image review **(update if necessary)** scanner properties:*

        ./data/sonar_scanner/sonar-scanner.properties

* *Build custom  sonar-scanner image with name **sonar**:*

        docker build -t sonar -f ./docker/sonar_scanner/Dockerfile .
        
* *Run custom  sonar-scanner image and go inside running container:*

        docker run -t -i --network="docker-shared-services-api" -v /$(pwd):/var/app sonar bash
  
* *Start sonar-scanner analyzing:*
    
        sonar-scanner -Dsonar.projectKey=lara-api -Dsonar.login=$LOGIN_TOKEN -Dsonar.host.url=http://sonarqube:9000

