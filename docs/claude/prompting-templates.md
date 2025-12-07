# 12. Prompting Templates

When working on this project with Claude, use these prompting patterns:

## For Creating New Features
```
Claude, I need to implement [feature name] from Phase [X], Section [X.Y].

Please:
1. Read the relevant migrations to understand the data structure
2. Show me the plan before making changes
3. Create [list specific files from the plan]
4. Write tests for this feature
5. Run Laravel Pint to format the code

Follow the project guidelines and use bilingual fields where needed.
```

## For Debugging
```
Claude, I'm encountering [error/issue].

Please:
1. Read the relevant files: [list files]
2. Identify the root cause
3. Explain the issue
4. Propose a fix
5. Ask before applying changes
```

## For Code Review
```
Claude, review the code in [file path].

Check for:
1. N+1 query issues
2. Missing eager loading
3. Bilingual field handling
4. Security vulnerabilities (SQL injection, XSS)
5. Missing validation
6. Code style (PSR-12)

Suggest improvements but don't modify files yet.
```

## For Testing
```
Claude, create tests for [feature/file].

Requirements:
1. Feature tests for API endpoints
2. Unit tests for business logic
3. Browser tests for critical user flows
4. Use factories for test data
5. Follow existing Pest test patterns

Show me the tests before creating files.
```

---
