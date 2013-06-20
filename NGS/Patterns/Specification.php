<?php
namespace NGS\Patterns;

require_once(__DIR__.'/IDomainObject.php');
require_once(__DIR__.'/../Client/DomainProxy.php');

use NGS\Client\DomainProxy;
use NGS\Name;

/**
 * Speficiation states a condition and is primarily used for searching.
 *
 * Specification is a predicate which states a condition. It can be used for
 * defining search conditions, validations and in many other parts of the
 * system.
 */
abstract class Specification implements IDomainObject
{
    /**
     * Search domain object using conditions in specification
     *
     * @param type $limit
     * @param type $offset
     * @param array $order
     * @return array Array of found objects, or empty array if none found
     */
    public function search($limit = null, $offset = null, array $order = null)
    {
        $domainObject = Name::parent($this);
        return DomainProxy::instance()->searchWithSpecification($domainObject, $this, $limit, $offset, $order);
    }

    /**
     * Count domain object using conditions in specification
     *
     * @return type
     */
    public function count()
    {
        return DomainProxy::instance()->countWithSpecification($this);
    }

    /**
     * Creates an instance of SearchBuilder from specification.
     *
     * @see NGS\Patterns\SearchBuilder
     * @return \NGS\Patterns\SearchBuilder
     */
    public function builder()
    {
        return new SearchBuilder($this);
    }
}