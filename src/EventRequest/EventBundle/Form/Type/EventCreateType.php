<?php

namespace EventRequest\EventBundle\Form\Type;

use EventRequest\EventBundle\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventCreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                    'label' => 'event.field.name'
                ))
            ->add('description', 'textarea', array(
                    'label' => 'event.field.description'
                ))
            ->add('address', null, array(
                    'label' => 'event.field.address'
                ))
            ->add('country', 'entity', array(
                    'class' => 'EventRequestEventBundle:Country',
                    'property' => 'name',
                    'empty_value' => 'page.filter.empty',
                    'mapped' => false
//                    'empty_value' => 'page.filter.empty',
//                    'apply_filter' => function(QueryInterface $filterQuery, $field, $values) {
//                            if (!empty($values['value'])) {
//                                $qb = $filterQuery->getQueryBuilder('e');
//                                $qb
//                                    ->innerJoin('e.city', 'city')
//                                    ->andWhere('city.country = :country')
//                                    ->setParameter('country', $values['value'])
//                                ;
//                            }
//                        }
                ))
            ->add('date', 'date', array(
                    'widget' => 'single_text',
                    'format' =>'dd.MM.yyyy'
                ))
            ->add('save', 'submit')
        ;

        $formModifier = function (FormInterface $form, Country $country = null) {
            $cities = null === $country ? array() : $country->getCities();

            $form->add('city', 'entity', array(
                    'class'       => 'EventRequestEventBundle:City',
                    'property'    => 'name',
                    'empty_value' => 'page.filter.empty',
                    'choices'     => $cities,
//                    'required' => false
                ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var City $city */
                $city = $event->getData();

                if ($city) {
                    $formModifier($event->getForm(), $city->getCountry());
                }

                $formModifier($event->getForm(), null);
            }
        );

        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                /** @var Country $country */
                $country = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $country);
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EventRequest\EventBundle\Entity\Event',
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'eventCreateType';
    }
}