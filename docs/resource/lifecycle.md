# Resource Lifecycle

Each operation on a resource follows a well-defined lifecycle.  
This flow ensures clear separation of concerns between reading data, applying business logic, and producing the final response.

```mermaid
flowchart TD
    A[Request] --> B[Routing & Metadata]
    B --> C[Provider]
    C --> D[Processor]
    D --> E[Responder]
    E --> F[Response]

    subgraph ProviderPhase [Provider Phase]
        direction LR
        C1[Load existing resource]
        C2[Create new instance]
        C3[Hydrate resource from request]
        C4[Validate resource]
    end

    subgraph ProcessorPhase [Processor Phase]
        direction LR
        D1[Apply business logic]
        D2[Persist resource]
        D3[Dispatch domain events]
    end

    subgraph ResponderPhase [Responder Phase]
        direction LR
        E1[Render template or redirect]
        E2[Return HTTP response]
    end

    C --> ProviderPhase
    D --> ProcessorPhase
    E --> ResponderPhase

    style ProviderPhase fill:#f4f8ff,stroke:#93c5fd,stroke-width:1px
    style ProcessorPhase fill:#fef9c3,stroke:#facc15,stroke-width:1px
    style ResponderPhase fill:#fef2f2,stroke:#f87171,stroke-width:1px
```

## Step-by-step explanation

### Routing & Metadata
The request is matched to a `Resource` and an `Operation` using metadata collected from attributes (eg `#[AsResource]`).  
This step defines which provider, processor, and responder should handle the request.

### Provider Phase
The provider is responsible for loading or creating the resource object, hydrating it from the request, and validating it before any business logic is applied.

**Example:** load from Doctrine, create a new instance, populate from request data, validate, or fetch from an external API.

### Processor Phase
The processor applies the business logic of the operation.

**Example:** persist the entity, execute domain services, or dispatch events.

You can use the default Doctrine processor, or implement your own for DDD use cases.

### Responder Phase
The responder produces the final output — it can render a Twig template or redirect to another route.
