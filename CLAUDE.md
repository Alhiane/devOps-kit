# Claude Rules for devOps-kit

## Context
This is a learning project for Docker and Kubernetes. The developer is junior level and actively learning DevOps concepts.

## Rules

### Teaching
- This is a learning environment — always explain new concepts, commands, and yaml fields before or right after using them
- Break down every new keyword, annotation, or config option — never assume knowledge
- When introducing something new in a yaml file, explain what it does and why it's needed
- Use simple analogies to explain complex concepts

### Code & Changes
- Always use the most recent version of files — never revert or suggest outdated approaches
- Never suggest deprecated syntax — always use the latest recommended way (e.g. `ingressClassName` not `kubernetes.io/ingress.class` annotation)
- When making changes to files, explain what changed and why
- Never make changes silently — always describe what you are doing

### Communication
- Do not suggest code snippets without explaining them
- Keep the developer in the loop at every step
- Ask before making multiple changes at once
- When there are multiple options, explain the tradeoffs before recommending one
- Recap what was learned at the end of each session

### Workflow
- Always explain the "why" not just the "what"
- Point out mistakes and best practices proactively
- If something is deprecated or not best practice, say so immediately
- Break tasks into clear numbered steps so the developer can follow along
