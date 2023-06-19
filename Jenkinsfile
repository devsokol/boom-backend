#!groovy
def space
if (BRANCH_NAME == "master")  {
    space = "prod"
} else if (BRANCH_NAME == "stage") {
    space = "stage"
} else if (BRANCH_NAME == "dev") {
    space = "dev"
}


pipeline{
    agent {
        kubernetes {
            yaml """
apiVersion: v1
kind: Pod
metadata:
  namespace: "${space}"
  labels:
    app: jenkins
spec:
  containers:
  - name: build
    image: docker:dind 
    command:
    - sleep
    args:
    - 999d
    tty: true
    volumeMounts:
    - mountPath: /conf/.env
      name: env
      subPath: .env
    - mountPath: /ecr
      name: ecr
    securityContext:
      privileged: true
  - name: deploy
    image: alpine/helm
    command:
    - '/bin/sleep'
    args:
    - 999d
    tty: true
    securityContext:
      privileged: true
  - name: kubectl
    image: bitnami/kubectl
    command:
    - sleep
    args:
    - 999d
    tty: true
    securityContext:
      runAsUser: 0
      privileged: true
  - name: aws
    image: amazon/aws-cli
    volumeMounts:
    - mountPath: /ecr
      name: ecr
    command:
    - sleep
    args:
    - 999d
    securityContext:
      runAsUser: 0
      privileged: true
    env:
    - name: AWS_ACCESS_KEY_ID
      valueFrom:
        secretKeyRef:
          name: ecr
          key: AWS_ACCESS_KEY_ID
    - name: AWS_SECRET_ACCESS_KEY
      valueFrom:
        secretKeyRef:
          name: ecr
          key: AWS_SECRET_ACCESS_KEY
  serviceAccountName: jenkins
  serviceAccount: jenkins
  volumes:
  - name: env
    configMap:
      name: boom-back-conf
  - name: ecr
    emptyDir: {}
"""
            defaultContainer 'build'
        }
    }
    stages{

        stage("Get registry") {
            steps {
                script {
                    container('aws') {
                        sh """
                        aws ecr get-login-password > /ecr/ecr
                        """
                    }
                }
            }
        } // END "aws login" stage

        stage("build back") {
            steps {
                script {
                    if (env.BRANCH_NAME == 'master') {
                        container('build') {
                            sh """
                            apk add git
                            /usr/local/bin/dockerd --experimental &
                            sleep 5
                            cp /conf/.env ./.env
                            cat .env
                            export DOCKER_HOST="unix:///var/run/docker.sock"
                            cat /ecr/ecr | docker login  --username AWS --password-stdin 903510684835.dkr.ecr.eu-north-1.amazonaws.com
                            DOCKER_CLI_EXPERIMENTAL=enabled DOCKER_BUILDKIT=1 docker image build --progress auto --squash -t  903510684835.dkr.ecr.eu-north-1.amazonaws.com/boom-back/${space}:\$(echo ${env.GIT_COMMIT} | head -c 8) .
                            docker push 903510684835.dkr.ecr.eu-north-1.amazonaws.com/boom-back/${space}:\$(echo ${env.GIT_COMMIT} | head -c 8)
                            """
                        }
                    } else if (env.BRANCH_NAME == 'stage') {
                        container('build') {
                            sh """
                            apk add git
                            /usr/local/bin/dockerd --experimental &
                            sleep 5
                            cp /conf/.env ./.env
                            cat .env
                            export DOCKER_HOST="unix:///var/run/docker.sock"
                            cat /ecr/ecr | docker login  --username AWS --password-stdin 903510684835.dkr.ecr.eu-north-1.amazonaws.com
                            DOCKER_CLI_EXPERIMENTAL=enabled DOCKER_BUILDKIT=1 docker image build --progress auto --squash -t  903510684835.dkr.ecr.eu-north-1.amazonaws.com/boom-back/${space}:\$(echo ${env.GIT_COMMIT} | head -c 8) .
                            docker push 903510684835.dkr.ecr.eu-north-1.amazonaws.com/boom-back/${space}:\$(echo ${env.GIT_COMMIT} | head -c 8)
                            """
                        }
                    }
                }

            }
        } // END "build" stage

        stage("deploy back") {
            steps {
                script {
                    if (env.BRANCH_NAME == 'master' ) {
                        container('deploy') {
                            sh """
                            apk add git
                            helm -n ${space} upgrade --install back ./.ci/helm --wait --set image.tag=\$(echo ${env.GIT_COMMIT} | head -c 8) --set image.repository=903510684835.dkr.ecr.eu-north-1.amazonaws.com/boom-back/${space}  --set autoscaling.enabled=true
                            """
                            }
                    } else if (env.BRANCH_NAME == 'stage' ) {
                        container('deploy') {
                            sh """
                            apk add git
                            helm -n ${space} upgrade --install back ./.ci/helm --wait --set image.tag=\$(echo ${env.GIT_COMMIT} | head -c 8) --set image.repository=903510684835.dkr.ecr.eu-north-1.amazonaws.com/boom-back/${space}  --set autoscaling.enabled=true
                            """
                            }
                    }
                }
            }
        } // END "deploy" stage

    }
    /* post{
        success{
            slackSend (color: '#00FF00', message: "SUCCESSFUL DEPLOY backend ON '${BRANCH_NAME}' : Job '${env.JOB_NAME} [${env.BUILD_NUMBER}]' (${env.BUILD_URL})")
        }
        failure{
            slackSend (color: '#FF0000', message: "FAILED:  DEPLOY backend ON  '${BRANCH_NAME}': Job '${env.JOB_NAME} [${env.BUILD_NUMBER}]' (${env.BUILD_URL})")
        }
    } */
}
