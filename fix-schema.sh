#!/bin/bash

# Define the input and output file names
input_file="schema.yaml"
output_file="schema-fix.yaml"

# Use yq to check if the nested paths exist and then fix it if necessary
echo "Removing the nested paths because it breaks the schema"
awk '
    /paths:/ {
        count++
    }
    count == 2 {
        count = 0;
        next
    }
    { print }
' "$input_file" > "$output_file"

yq e '.paths."/api/offers".get.parameters = .paths."/api/projects".get.parameters' "$output_file" > schema-fix-fix.yaml


# replace the original file with the fixed file
echo "Replacing the original file with the fixed file"
rm schema-fix.yaml
mv schema-fix-fix.yaml schema.yaml
