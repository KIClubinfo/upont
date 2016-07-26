api_key=

application_id=25357270

curl -X POST "https://api.newrelic.com/v2/applications/$application_id/deployments.json" \
     -H "X-Api-Key:$api_key" -i \
     -H 'Content-Type: application/json' \
     -d \
"{
  \"deployment\": {
    \"revision\": \"$(git rev-parse HEAD)\",
    \"description\": \"$(git describe --tag) $(git log -1 --oneline)\",
    \"user\": \"odin\"
  }
}"
