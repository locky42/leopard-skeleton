#!/bin/bash

# Find all .example files and copy them if the target file doesn't exist
for example_file in $(find . -type f -name "*.example"); do
    target_file="${example_file%.example}" # Remove the .example extension
    if [ ! -f "$target_file" ]; then
        echo "Copying $example_file to $target_file"
        cp "$example_file" "$target_file"
    else
        # Extract keys from both files and compare them
        example_keys=$(grep -o '^[^#]*=' "$example_file" | sed 's/=.*//')
        target_keys=$(grep -o '^[^#]*=' "$target_file" | sed 's/=.*//')

        # Find keys that are in the example file but not in the target file
        missing_keys=$(comm -23 <(echo "$example_keys" | sort) <(echo "$target_keys" | sort))
        extra_keys=$(comm -13 <(echo "$example_keys" | sort) <(echo "$target_keys" | sort))

        if [ -n "$missing_keys" ] || [ -n "$extra_keys" ]; then
            echo "Key differences found between $example_file and $target_file:"
            if [ -n "$missing_keys" ]; then
                echo "Keys in $example_file but not in $target_file:"
                echo "$missing_keys"
            fi
            if [ -n "$extra_keys" ]; then
                echo "Keys in $target_file but not in $example_file:"
                echo "$extra_keys"
            fi
        else
            echo "Skipping $target_file, keys are identical"
        fi
    fi
done

echo "All .example files have been processed."
