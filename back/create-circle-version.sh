#!/bin/sh
set -eux

VERSION_REGEX="^v([0-9]+)\.([0-9]+)\.([0-9]{1}.*)$"
if [[ ${CIRCLE_TAG:-} =~ ${VERSION_REGEX} ]]; then
    VERSION_MAJOR="${BASH_REMATCH[1]}"
    VERSION_MINOR="${BASH_REMATCH[2]}"
    VERSION_PATCH="${BASH_REMATCH[3]}"
else
    VERSION_MAJOR=3
    VERSION_MINOR=0
    VERSION_PATCH=0
fi
VERSION_HASH=${CIRCLE_SHA1}
VERSION_DATE=$(date +%s)

cat > ./config/version.yaml <<EOL
parameters:
  version-info:
    major: ${VERSION_MAJOR}
    minor: ${VERSION_MINOR}
    patch: ${VERSION_PATCH}
    hash: ${VERSION_HASH}
    date: ${VERSION_DATE}
EOL

cat ./config/version.yaml
