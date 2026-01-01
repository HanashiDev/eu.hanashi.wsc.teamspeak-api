jq -S '.packages | map({ (.name) : (.version) }) |add' < composer.lock

composer bump